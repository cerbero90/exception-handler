<?php

namespace Cerbero\ExceptionHandler;

use Exception;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Mockery;
use PHPUnit\Framework\TestCase;

/**
 * The handler decorator test.
 *
 */
class HandlerDecoratorTest extends TestCase
{
    /**
     * @test
     */
    public function proxiesShouldReportWhenImplementedInDefaultExceptionHandler()
    {
        $exception = new Exception;
        $defaultHandler = Mockery::mock(ExceptionHandler::class)
            ->shouldReceive('shouldReport')
            ->with($exception)
            ->andReturn(true)
            ->getMock();

        $handler = new HandlerDecorator($defaultHandler, new HandlersRepository);

        $this->assertTrue($handler->shouldReport($exception));

        Mockery::close();
    }

    /**
     * @test
     */
    public function proxiesCallsToDefaultExceptionHandler()
    {
        $defaultHandler = Mockery::mock(ExceptionHandler::class)
            ->shouldReceive(['foo' => 'bar'])
            ->getMock();

        $handler = new HandlerDecorator($defaultHandler, new HandlersRepository);

        $this->assertSame('bar', $handler->foo());

        Mockery::close();
    }
}
