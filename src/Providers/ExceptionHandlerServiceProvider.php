<?php

namespace Cerbero\ExceptionHandler\Providers;

use Cerbero\ExceptionHandler\HandlerDecorator;
use Cerbero\ExceptionHandler\HandlersRepository;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Support\ServiceProvider;

/**
 * The exception handler service provider.
 *
 */
class ExceptionHandlerServiceProvider extends ServiceProvider
{
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->registerExceptionHandlersRepository();

        $this->extendExceptionHandler();
    }

    /**
     * Register the custom exception handlers repository.
     *
     * @return void
     */
    private function registerExceptionHandlersRepository()
    {
        $this->app->singleton(HandlersRepository::class, HandlersRepository::class);
    }

    /**
     * Extend the Laravel default exception handler.
     *
     * @return void
     */
    private function extendExceptionHandler()
    {
        $this->app->extend(ExceptionHandler::class, function (ExceptionHandler $handler, $app) {
            return new HandlerDecorator($handler, $app[HandlersRepository::class]);
        });
    }
}
