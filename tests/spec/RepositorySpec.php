<?php

namespace spec\Cerbero\ExceptionHandler;

use Exception;
use LogicException;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class RepositorySpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType('Cerbero\ExceptionHandler\Repository');
    }

    /**
     * @testdox    It adds reporters.
     *
     * @author    Andrea Marco Sartori
     * @return    void
     */
    public function it_adds_reporters()
    {
        $reporter = function () {};

        $this->addReporter($reporter)->shouldReturn(1);
        $this->addReporter($reporter)->shouldReturn(2);
        $this->addReporter($reporter)->shouldReturn(3);
    }

    /**
     * @testdox    It adds renderers.
     *
     * @author    Andrea Marco Sartori
     * @return    void
     */
    public function it_adds_renderers()
    {
        $renderer = function () {};

        $this->addRenderer($renderer)->shouldReturn(1);
        $this->addRenderer($renderer)->shouldReturn(2);
        $this->addRenderer($renderer)->shouldReturn(3);
    }

    /**
     * @testdox    It adds console renderers.
     *
     * @author    Andrea Marco Sartori
     * @return    void
     */
    public function it_adds_console_renderers()
    {
        $renderer = function () {};

        $this->addConsoleRenderer($renderer)->shouldReturn(1);
        $this->addConsoleRenderer($renderer)->shouldReturn(2);
        $this->addConsoleRenderer($renderer)->shouldReturn(3);
    }

    /**
     * @testdox    It retrieves only reporters of a given exception.
     *
     * @author    Andrea Marco Sartori
     * @return    void
     */
    public function it_retrieves_only_reporters_of_a_given_exception()
    {
        $reporter1 = function (Exception $e) {};
        $reporter2 = function (LogicException $e) {};

        $this->addReporter($reporter1);
        $this->addReporter($reporter2);

        $this->getReportersFor(new Exception)->shouldHaveCount(1);
    }

    /**
     * @testdox    It retrieves only renderers of a given exception.
     *
     * @author    Andrea Marco Sartori
     * @return    void
     */
    public function it_retrieves_only_renderers_of_a_given_exception()
    {
        $renderer1 = function (Exception $e) {};
        $renderer2 = function (LogicException $e) {};

        $this->addRenderer($renderer1);
        $this->addRenderer($renderer2);

        $this->getRenderersFor(new Exception)->shouldHaveCount(1);
    }

    /**
     * @testdox    It retrieves only console renderers of a given exception.
     *
     * @author    Andrea Marco Sartori
     * @return    void
     */
    public function it_retrieves_only_console_renderers_of_a_given_exception()
    {
        $renderer1 = function (Exception $e) {};
        $renderer2 = function (LogicException $e) {};

        $this->addConsoleRenderer($renderer1);
        $this->addConsoleRenderer($renderer2);

        $this->getConsoleRenderersFor(new Exception)->shouldHaveCount(1);
    }
}
