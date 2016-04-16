<?php

namespace spec\Cerbero\ExceptionHandler;

use Cerbero\ExceptionHandler\Repository;
use Closure;
use Exception;
use Illuminate\Contracts\Debug\ExceptionHandler;
use PhpSpec\Exception\Example\FailureException;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class DecoratorSpec extends ObjectBehavior
{
    /**
     * Initialise the test.
     *
     * @author    Andrea Marco Sartori
     * @param    Illuminate\Contracts\Debug\ExceptionHandler    $handler
     * @param    Cerbero\ExceptionHandler\Repository    $handlers
     * @return    void
     */
    public function let(ExceptionHandler $handler, Repository $handlers)
    {
        $this->beConstructedWith($handler, $handlers);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType('Cerbero\ExceptionHandler\Decorator');
    }

    /**
     * @testdox    It calls custom reporters during the report of an exception.
     *
     * @author    Andrea Marco Sartori
     * @return    void
     */
    public function it_calls_custom_reporters_during_the_report_of_an_exception($handler, $handlers)
    {
        $e = new Exception;

        $reporter = function ($e) {
            static $i = 0;
            return $i++;
        };

        $handlers->getReportersFor($e)->willReturn([$reporter]);

        $handler->report($e)->shouldBeCalled();

        $this->report($e);

        if ($reporter($e) < 1) {
            throw new FailureException('Failed asserting that the reporter has been called.');
        }
    }

    /**
     * @testdox    It registers custom reporters.
     *
     * @author    Andrea Marco Sartori
     * @return    void
     */
    public function it_registers_custom_reporters($handlers)
    {
        $handler = function () {};

        $handlers->addReporter($handler)->willReturn(1);

        $this->reporter($handler)->shouldReturn(1);
    }

    /**
     * @testdox    It calls custom renderers during the render of an exception.
     *
     * @author    Andrea Marco Sartori
     * @return    void
     */
    public function it_calls_custom_renderers_during_the_render_of_an_exception($handler, $handlers)
    {
        $e = new Exception;

        $renderer = function ($e) {
            static $i = 0;
            return $i++;
        };

        $handlers->getRenderersFor($e)->willReturn([$renderer]);

        $this->render('request', $e);

        if ($renderer($e) < 1) {
            throw new FailureException('Failed asserting that the renderer has been called.');
        }
    }

    /**
     * @testdox    It registers custom renderers.
     *
     * @author    Andrea Marco Sartori
     * @return    void
     */
    public function it_registers_custom_renderers($handlers)
    {
        $handler = function () {};

        $handlers->addRenderer($handler)->willReturn(1);

        $this->renderer($handler)->shouldReturn(1);
    }

    /**
     * @testdox    It calls custom console renderers during the render of an exception.
     *
     * @author    Andrea Marco Sartori
     * @return    void
     */
    public function it_calls_custom_console_renderers_during_the_render_of_an_exception($handler, $handlers)
    {
        $e = new Exception;

        $renderer = function ($e) {
            static $i = 0;
            return $i++;
        };

        $handlers->getConsoleRenderersFor($e)->willReturn([$renderer]);

        $handler->renderForConsole('output', $e)->shouldBeCalled();

        $this->renderForConsole('output', $e);

        if ($renderer($e) < 1) {
            throw new FailureException('Failed asserting that the console renderer has been called.');
        }
    }

    /**
     * @testdox    It registers custom console renderers.
     *
     * @author    Andrea Marco Sartori
     * @return    void
     */
    public function it_registers_custom_console_renderers($handlers)
    {
        $handler = function () {};

        $handlers->addConsoleRenderer($handler)->willReturn(1);

        $this->consoleRenderer($handler)->shouldReturn(1);
    }
}
