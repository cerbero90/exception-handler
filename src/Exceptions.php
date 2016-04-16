<?php

namespace Cerbero\ExceptionHandler;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Cerbero\ExceptionHandler\Decorator
 */
class Exceptions extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'exceptions';
    }
}
