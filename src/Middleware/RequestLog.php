<?php
/**
 * creator: maigohuang
 */
namespace EasyLib\Middleware;
use EasyLib\RunTimeUtil;

class RequestLog extends BaseMiddleware
{
    public function __invoke($request, $response, $next) {
        $t = new RunTimeUtil(json_encode($_SERVER));
        $r = $next($request, $response);
        return $r->withHeader('X-Request-Id', REQUEST_ID);
    }
}
