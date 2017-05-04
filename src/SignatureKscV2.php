<?php
namespace EasyLib;

use GuzzleHttp\Psr7;
use Psr\Http\Message\RequestInterface;

class SignatureKscV2 
{
    use SignatureTrait;
    const TIME_FORMAT = 'YmdHis';

    public function signRequest(
        RequestInterface $request,
        $ak, $sk
    ) {
        $uri = $request->getUri();
        $headers = $request->getHeaders();

        $query = Psr7\parse_query($uri->getQuery());
        $query['Timestamp'] = gmdate(self::TIME_FORMAT);
        $query['AccessKey'] = $ak;

        if (isset($headers['Content-Type']) && strtolower(current($headers['Content-Type'])) == 'application/x-www-form-urlencoded') {
            $body = $request->getBody()->getContents();
            $body_array = explode('&', $body);
            $data = [];
            foreach ($body_array as $d) {
                list($k, $v) = explode('=', $d);
                $data[$k] = $v;
            }
            $signature = $this->sign($request->getMethod(), $uri->getHost(), $uri->getPath(), array_merge($data, $query), $sk);
        }else {
            $signature = $this->sign($request->getMethod(), $uri->getHost(), $uri->getPath(), $query, $sk);
        }
        $query['Signature'] = $signature;

        $parsed = [
            'method' => $request->getMethod(),
            'headers' => $headers,
            'body' => $request->getBody(),
            'uri' => $uri,
            'query' => $query,
            'version' => $request->getProtocolVersion(),
        ];

        //return $request;
        return $this->buildRequest($parsed);
    }

    private function sign($method, $host, $path, $data, $sk)
    {
        $canonicalizedString = '';
        ksort($data);
        foreach ($data as $key=>$value) {
            $key = rawurlencode($key);
            $value = rawurlencode($value);
            $canonicalizedString .= "$key=$value&";
        }
        $canonicalizedString = substr($canonicalizedString, 0, -1);

        $signString = "$method$host$path?$canonicalizedString";

        $sign = hash_hmac('sha256', $signString, $sk);

        return $sign;
    }


    private function buildRequest(array $req)
    {
        if ($req['query']) {
            $req['uri'] = $req['uri']->withQuery(Psr7\build_query($req['query']));
        }

        return new Psr7\Request(
            $req['method'],
            $req['uri'],
            $req['headers'],
            $req['body']
        );
    }
}
