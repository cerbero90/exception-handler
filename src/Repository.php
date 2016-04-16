<?php

namespace Cerbero\ExceptionHandler;

use Closure;
use Exception;
use ReflectionFunction;

/**
 * Custom exception handlers repository.
 *
 * @author    Andrea Marco Sartori
 */
class Repository
{
    /**
     * @author    Andrea Marco Sartori
     * @var        array    $reporters    List of handlers reporting exceptions.
     */
    protected $reporters = [];

    /**
     * @author    Andrea Marco Sartori
     * @var        array    $renderers    List of handlers rendering exceptions.
     */
    protected $renderers = [];

    /**
     * @author    Andrea Marco Sartori
     * @var        array    $consoleRenderers    List of handlers rendering exceptions to the console.
     */
    protected $consoleRenderers = [];

    /**
     * Register a custom handler to report exceptions.
     *
     * @author    Andrea Marco Sartori
     * @param    \Closure    $reporter
     * @return    integer
     */
    public function addReporter(Closure $reporter)
    {
        return array_unshift($this->reporters, $reporter);
    }

    /**
     * Register a custom handler to render exceptions.
     *
     * @author    Andrea Marco Sartori
     * @param    \Closure    $renderer
     * @return    integer
     */
    public function addRenderer(Closure $renderer)
    {
        return array_unshift($this->renderers, $renderer);
    }

    /**
     * Register a custom handler to render exceptions to the console.
     *
     * @author    Andrea Marco Sartori
     * @param    \Closure    $renderer
     * @return    integer
     */
    public function addConsoleRenderer(Closure $renderer)
    {
        return array_unshift($this->consoleRenderers, $renderer);
    }

    /**
     * Retrieve all the reporters that handle the given exception.
     *
     * @param  \Exception  $e
     * @return array
     */
    public function getReportersFor(Exception $e)
    {
        return array_filter($this->reporters, $this->handlesException($e));
    }

    /**
     * Retrieve the filter to get only handlers that handle the given exception.
     *
     * @param  \Exception  $e
     * @return \Closure
     */
    protected function handlesException(Exception $e)
    {
        return function (Closure $handler) use ($e) {
            $reflection = new ReflectionFunction($handler);

            if (! $params = $reflection->getParameters()) {
                return true;
            }

            return $params[0]->getClass() ? $params[0]->getClass()->isInstance($e) : true;
        };
    }

    /**
     * Retrieve all the renderers that handle the given exception.
     *
     * @param  \Exception  $e
     * @return array
     */
    public function getRenderersFor(Exception $e)
    {
        return array_filter($this->renderers, $this->handlesException($e));
    }

    /**
     * Retrieve all the renderers for console that handle the given exception.
     *
     * @param  \Exception  $e
     * @return array
     */
    public function getConsoleRenderersFor(Exception $e)
    {
        return array_filter($this->consoleRenderers, $this->handlesException($e));
    }
}
