<?php

namespace Cerbero\ExceptionHandler;

use Exception;
use Cerbero\ExceptionHandler\Providers\ExceptionHandlerServiceProvider;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Http\Request;
use Orchestra\Testbench\TestCase;
use Symfony\Component\Console\Output\NullOutput;

/**
 * The exception handler test.
 *
 */
class ExceptionHandlerTest extends TestCase
{
    /**
     * Retrieve the package service providers
     *
     * @param \Illuminate\Contracts\Container\Container $app
     * @return array
     */
    protected function getPackageProviders($app)
    {
        return [ExceptionHandlerServiceProvider::class];
    }

    /**
     * @test
     */
    public function reportsCustomException()
    {
        $exceptionHandler = $this->app->make(ExceptionHandler::class);

        $exceptionHandler->reporter(function (ReportableException $e) {
            return 'foo';
        });

        $this->assertSame('foo', $exceptionHandler->report(new ReportableException));
    }

    /**
     * @test
     */
    public function rendersCustomException()
    {
        $exceptionHandler = $this->app->make(ExceptionHandler::class);

        $exceptionHandler->renderer(function (RenderableException $e) {
            return 'bar';
        });

        $this->assertSame('bar', $exceptionHandler->render(new Request, new RenderableException));
    }

    /**
     * @test
     */
    public function rendersCustomExceptionInConsole()
    {
        $exceptionHandler = $this->app->make(ExceptionHandler::class);

        $exceptionHandler->consoleRenderer(function (ConsoleRenderableException $e) {
            return 'baz';
        });

        $this->assertSame('baz', $exceptionHandler->renderForConsole(new NullOutput, new ConsoleRenderableException));
    }
}

class ReportableException extends Exception
{ }

class RenderableException extends Exception
{ }

class ConsoleRenderableException extends Exception
{ }
