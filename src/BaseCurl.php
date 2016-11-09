<?php
/**
 * creator: maigohuang
 */ 
namespace EasyLib;

use GuzzleHttp\Client;
use GuzzleHttp\Middleware;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Handler\CurlHandler;
use GuzzleHttp\Exception\ClientException;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

abstract class BaseCurl extends Singleton
{
    const DEFAULT_TIMEOUT = 5;
    protected $apiList = [];

    protected $client = null;
    protected $stack = null;

    public function __construct()
    {
        //$this->stack = new HandlerStack();
        //$this->stack->setHandler(new CurlHandler());
        $this->stack = HandlerStack::create();
        $this->stack->push($this->replaceUri());
        $this->stack->push($this->logRpc());

        $config = $this->getConfig();
        $env = $config['host'] . 'Host';

        $this->client = new Client([
            'handler' => $this->stack,
            'base_uri' => $this->$env,
        ]);
    }

    abstract protected function getConfig();

    public function request($api, array $config = [])
    {
        $config_api = isset($this->apiList[$api]) ? $this->apiList[$api] : false;

        $defaultConfig = $this->getConfig();
        $config = $this->configMerge($defaultConfig['config'], $config_api['config'], $config);
        $info = array_merge($defaultConfig, $config_api);
        $info['config'] = $config;

        $method = $info['method'];
        try {
            $response = $this->client->request($method, $info['url'], $info['config']);
            $result = (string)$response->getBody();
            $httpCode = $response->getStatusCode();
        }catch(ClientException $exception) {
            $result = (string)$exception->getResponse()->getBody();
            $httpCode = $exception->getResponse()->getStatusCode();
        }

        return [$result, $httpCode];
    }

    private function configMerge($c1, $c2, $c3)
    {
        $result = $c1;
        foreach ($c2 as $k=>$v) {
            if (isset($result[$k]) && is_array($result[$k]) && is_array($v)) {
                $result[$k] = array_merge($result[$k], $v);
            }else {
                $result[$k] = $v;
            }
        }

        foreach ($c3 as $k=>$v) {
            if (isset($result[$k]) && is_array($result[$k]) && is_array($v)) {
                $result[$k] = array_merge($result[$k], $v);
            }else {
                $result[$k] = $v;
            }
        }
        return $result;
    }

    protected function replaceUri()
    {
        return function (callable $handler) {
            return function (RequestInterface $request, array $options) use ($handler) {
                if (isset($options['replace'])) {
                    $replace = $options['replace'];
                    $uri = (string)$request->getUri();

                    $func = function($matches) use($replace) {
                        $key = substr($matches[0], 1, -1);
                        return $replace[$key];
                    };
                    $uri = preg_replace_callback('/\{.*?\}/', $func, $uri);

                    $func2 = function($matches) use($replace) {
                        $key = substr($matches[0], 3, -3);
                        return $replace[$key];
                    };
                    $uri = preg_replace_callback('/%7B.*?%7D/', $func2, $uri);
                    $request = $request->withUri(new Uri($uri));
                }
                return $handler($request, $options);
            };
        };
    }

    protected function logRpc()
    {   
        return function(callable $handler) {
            return function(RequestInterface $request, array $options)
            use ($handler) {
                $promise = $handler($request, $options);
                $spent = new RunTimeUtil();
                $spent->start();
                return $promise->then(
                    function (ResponseInterface $response) use ($request, $spent) {
                        $cost = $spent->spent();
                        $req = $this->logRequest($request);
                        $res = $this->logResponse($response);
                        $log = array_merge($req, $res, ['cost#' . $cost]);
                        Log::info('curl', implode('#|', $log));
                        return $response;
                    }   
                );  
            };  
        };  
    }

    protected function logRequest(RequestInterface $r)
    {
        $arr = ['curl', '-X'];
        $arr[] = $r->getMethod();
        foreach ($r->getHeaders() as $name=>$values) {
            foreach ($values as $value) {
                $arr[] = '-H';
                $arr[] = "'$name:$value'";
            }
        }
        $body = (string)$r->getBody();
        if ($body) {
            $arr[] = '-d';
            $arr[] = "'$body'";
        }
        $uri = (string)$r->getUri();
        $arr[] = "'$uri'";

        $log = [
            'curl#' . implode(' ', $arr)
        ];
        return $log;
    }

    protected function logResponse(ResponseInterface $response)
    {
        return [
            'httpCode#' . $response->getStatusCode(),
            'reasonPhrase' . $response->getReasonPhrase(),
            'response#' . (string)$response->getBody(),
        ];
    }


    /**
     * fn1 => function(RequestInterface $request, $options) => RequestInterface
     * fn2 => function(ResponseInterface $response) => ResponseInterface
     */
    protected static function mapRequestAndResponse(callable $fn1, callable $fn2)
    {
        return function(callable $handler) use($fn1, $fn2)
        {
            return function ($request, array $options) use ($handler, $fn1, $fn2) 
            {
                $promise = $handler($fn1($request, $options), $options);
                return $promise->then($fn2);
            };
        };
    }
}
