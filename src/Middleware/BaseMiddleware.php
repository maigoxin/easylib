<?php
/**
 * creator: maigohuang
 */
namespace EasyLib\Middleware;

use EasyLib\Log;

abstract class BaseMiddleware
{

    public function __construct()
    {
        $class = get_class($this);
        Log::info("middleware $class");
    }
}
