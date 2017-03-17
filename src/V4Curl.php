<?php
/**
 * creator: maigohuang
 */ 
namespace EasyLib;

use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Handler\CurlHandler;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

abstract class V4Curl extends BaseCurl 
{
    public function __construct()
    {
        $this->stack = HandlerStack::create();
        $this->stack->push($this->replaceUri());
        $this->stack->push($this->v4Sign());
        $this->stack->push($this->logRpc());

        $config = $this->getConfig();
        $env = $config['host'] . 'Host';

        $this->client = new Client([
            'handler' => $this->stack,
            'base_uri' => $this->$env,
        ]);
    }

    protected function v4Sign()
    {
        return function (callable $handler) {
            return function (RequestInterface $request, array $options) use ($handler) {
                $v4 = new SignatureV4();
                $credentials = $options['v4_credentials'];
                $request = $v4->signRequest($request, $credentials);
                return $handler($request, $options);
            };
        };
    }
}
