<?php

namespace Cerbero\ExceptionHandler;

use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Foundation\AliasLoader;
use Illuminate\Support\ServiceProvider as Provider;

/**
 * Service provider for the exception handler.
 *
 * @author    Andrea Marco Sartori
 */
class ServiceProvider extends Provider
{
    /**
     * Add an alias for the exception handler facade.
     *
     * @author    Andrea Marco Sartori
     * @return    void
     */
    public function boot()
    {
        AliasLoader::getInstance()->alias('Exceptions', Exceptions::class);
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->aliasExceptionHandler();

        $this->registerExceptionHandlersRepository();

        $this->extendExceptionHandler();
    }

    /**
     * Create an alias for the Laravel default exception handler.
     *
     * @author    Andrea Marco Sartori
     * @return    void
     */
    private function aliasExceptionHandler()
    {
        $this->app->alias(ExceptionHandler::class, 'exceptions');
    }

    /**
     * Register the custom exception handlers repository.
     *
     * @author    Andrea Marco Sartori
     * @return    void
     */
    private function registerExceptionHandlersRepository()
    {
        $this->app->singleton('exceptions.repository', Repository::class);
    }

    /**
     * Extend the Laravel default exception handler.
     *
     * @author    Andrea Marco Sartori
     * @return    void
     */
    private function extendExceptionHandler()
    {
        $this->app->extend(ExceptionHandler::class, function ($handler, $app) {
            return new Decorator($handler, $app['exceptions.repository']);
        });
    }
}
