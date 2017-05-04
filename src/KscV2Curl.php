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

abstract class KscV2Curl extends BaseCurl 
{
    public function __construct()
    {
        $this->stack = HandlerStack::create();
        $this->stack->push($this->replaceUri());
        $this->stack->push($this->kscV2Sign());
        $this->stack->push($this->logRpc());

        $config = $this->getConfig();
        $env = $config['host'] . 'Host';

        $this->client = new Client([
            'handler' => $this->stack,
            'base_uri' => $this->$env,
        ]);
    }

    protected function kscV2Sign()
    {
        return function (callable $handler) {
            return function (RequestInterface $request, array $options) use ($handler) {
                $kscv2 = new SignatureKscV2();
                $credentials = $options['kscv2_credentials'];
                $request = $kscv2->signRequest($request, $credentials['ak'], $credentials['sk']);
                return $handler($request, $options);
            };
        };
    }
}
