<?php

namespace Cerbero\ExceptionHandler;

use Exception;
use Cerbero\ExceptionHandler\Providers\ExceptionHandlerServiceProvider;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
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
    public function proxiesReportingToDefaultExceptionHandler()
    {
        $exceptionHandler = $this->app->make(ExceptionHandler::class);

        $this->assertNull($exceptionHandler->report(new ReportableException));
    }

    /**
     * @test
     */
    public function ignoresReportersWithNoParameters()
    {
        $exceptionHandler = $this->app->make(ExceptionHandler::class);

        $exceptionHandler->reporter(function () {
            return 'no params';
        });

        $this->assertNull($exceptionHandler->report(new ReportableException));
    }

    /**
     * @test
     */
    public function reportsAnyException()
    {
        $exceptionHandler = $this->app->make(ExceptionHandler::class);

        $exceptionHandler->reporter(function (Exception $e) {
            return 'any1';
        });

        $this->assertSame('any1', $exceptionHandler->report(new ReportableException));
    }

    /**
     * @test
     */
    public function reportsAnyExceptionWithNoTypeHint()
    {
        $exceptionHandler = $this->app->make(ExceptionHandler::class);

        $exceptionHandler->reporter(function ($e) {
            return 'any2';
        });

        $this->assertSame('any2', $exceptionHandler->report(new ReportableException));
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
    public function proxiesRenderingToDefaultExceptionHandler()
    {
        $exceptionHandler = $this->app->make(ExceptionHandler::class);
        $version = substr($this->app->version(), 0, 3);

        if ($version === '5.2') {
            try {
                $exceptionHandler->render(new Request, new RenderableException);
            } catch (Exception $e) {
                $this->assertInstanceOf(RenderableException::class, $e);
            }
        } else {
            $response = $exceptionHandler->render(new Request, new RenderableException);

            $this->assertInstanceOf(Response::class, $response);
        }
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
    public function proxiesConsoleRenderingToDefaultExceptionHandler()
    {
        $exceptionHandler = $this->app->make(ExceptionHandler::class);

        $this->assertNull($exceptionHandler->renderForConsole(new NullOutput, new RenderableException));
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
