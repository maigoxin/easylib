<?php
/**
 * creator: maigohuang
 */
namespace EasyLib\Middleware;

class Cross extends BaseMiddleware
{
    public function __invoke($request, $response, $next) {
        $r = $next($request, $response);
        return $r->withHeader('Access-Control-Allow-Origin', '*')
            ->withHeader('Access-Control-Allow-Method', 'GET,PUT,POST')
            ->withHeader('Access-Control-Allow-Header', 'X-Requested-With')
            ->withHeader('Access-Control-Allow-Credentials', true);
    }
}
