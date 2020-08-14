<?php

namespace Cerbero\ExceptionHandler;

use Throwable;
use ReflectionFunction;

/**
 * The handlers repository.
 *
 */
class HandlersRepository
{
    /**
     * The custom handlers reporting exceptions.
     *
     * @var array
     */
    protected $reporters = [];

    /**
     * The custom handlers rendering exceptions.
     *
     * @var array
     */
    protected $renderers = [];

    /**
     * The custom handlers rendering exceptions in console.
     *
     * @var array
     */
    protected $consoleRenderers = [];

    /**
     * Register a custom handler to report exceptions
     *
     * @param callable $reporter
     * @return int
     */
    public function addReporter(callable $reporter)
    {
        return array_unshift($this->reporters, $reporter);
    }

    /**
     * Register a custom handler to render exceptions
     *
     * @param callable $renderer
     * @return int
     */
    public function addRenderer(callable $renderer)
    {
        return array_unshift($this->renderers, $renderer);
    }

    /**
     * Register a custom handler to render exceptions in console
     *
     * @param callable $renderer
     * @return int
     */
    public function addConsoleRenderer(callable $renderer)
    {
        return array_unshift($this->consoleRenderers, $renderer);
    }

    /**
     * Retrieve all reporters handling the given exception
     *
     * @param \Throwable $e
     * @return array
     */
    public function getReportersByException(Throwable $e)
    {
        return array_filter($this->reporters, function (callable $handler) use ($e) {
            return $this->handlesException($handler, $e);
        });
    }

    /**
     * Determine whether the given handler can handle the provided exception
     *
     * @param callable $handler
     * @param \Throwable $e
     * @return bool
     */
    protected function handlesException(callable $handler, Throwable $e)
    {
        $reflection = new ReflectionFunction($handler);

        if (!$params = $reflection->getParameters()) {
            return false;
        }

        return $params[0]->getClass() ? $params[0]->getClass()->isInstance($e) : true;
    }

    /**
     * Retrieve all renderers handling the given exception
     *
     * @param \Throwable $e
     * @return array
     */
    public function getRenderersByException(Throwable $e)
    {
        return array_filter($this->renderers, function (callable $handler) use ($e) {
            return $this->handlesException($handler, $e);
        });
    }

    /**
     * Retrieve all console renderers handling the given exception
     *
     * @param \Throwable $e
     * @return array
     */
    public function getConsoleRenderersByException(Throwable $e)
    {
        return array_filter($this->consoleRenderers, function (callable $handler) use ($e) {
            return $this->handlesException($handler, $e);
        });
    }
}
