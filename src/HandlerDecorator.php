<?php

namespace Cerbero\ExceptionHandler;

use Throwable;
use Illuminate\Contracts\Debug\ExceptionHandler;

/**
 * The exception handler decorator.
 *
 */
class HandlerDecorator implements ExceptionHandler
{
    /**
     * The default Laravel exception handler.
     *
     * @var \Illuminate\Contracts\Debug\ExceptionHandler
     */
    protected $defaultHandler;

    /**
     * The custom handlers repository.
     *
     * @var \Cerbero\ExceptionHandler\HandlersRepository
     */
    protected $repository;

    /**
     * Set the dependencies.
     *
     * @param \Illuminate\Contracts\Debug\ExceptionHandler $defaultHandler
     * @param \Cerbero\ExceptionHandler\HandlersRepository $repository
     */
    public function __construct(ExceptionHandler $defaultHandler, HandlersRepository $repository)
    {
        $this->defaultHandler = $defaultHandler;

        $this->repository = $repository;
    }

    /**
     * Report or log an exception.
     *
     * @param \Throwable $e
     * @return mixed
     *
     * @throws \Throwable
     */
    public function report(Throwable $e)
    {
        foreach ($this->repository->getReportersByException($e) as $reporter) {
            if ($report = $reporter($e)) {
                return $report;
            }
        }

        $this->defaultHandler->report($e);
    }

    /**
     * Register a custom handler to report exceptions
     *
     * @param callable $callback
     * @return int
     */
    public function reporter(callable $reporter)
    {
        return $this->repository->addReporter($reporter);
    }

    /**
     * Render an exception into a response.
     *
     * @param \Illuminate\Http\Request  $request
     * @param \Throwable $e
     * @return \Illuminate\Http\Response|\Symfony\Component\HttpFoundation\Response
     */
    public function render($request, Throwable $e)
    {
        foreach ($this->repository->getRenderersByException($e) as $renderer) {
            if ($render = $renderer($e, $request)) {
                return $render;
            }
        }

        return $this->defaultHandler->render($request, $e);
    }

    /**
     * Register a custom handler to render exceptions
     *
     * @param callable $callback
     * @return int
     */
    public function renderer(callable $renderer)
    {
        return $this->repository->addRenderer($renderer);
    }

    /**
     * Render an exception to the console.
     *
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     * @param \Throwable $e
     * @return mixed
     */
    public function renderForConsole($output, Throwable $e)
    {
        foreach ($this->repository->getConsoleRenderersByException($e) as $renderer) {
            if ($render = $renderer($e, $output)) {
                return $render;
            }
        }

        $this->defaultHandler->renderForConsole($output, $e);
    }

    /**
     * Register a custom handler to render exceptions in console
     *
     * @param callable $callback
     * @return int
     */
    public function consoleRenderer(callable $renderer)
    {
        return $this->repository->addConsoleRenderer($renderer);
    }

    /**
     * Determine if the exception should be reported.
     *
     * @param \Throwable $e
     * @return bool
     */
    public function shouldReport(Throwable $e)
    {
        return $this->defaultHandler->shouldReport($e);
    }

    /**
     * Proxy other calls to default Laravel exception handler
     *
     * @param string $name
     * @param array $parameters
     * @return mixed
     */
    public function __call($name, $parameters)
    {
        return call_user_func_array([$this->defaultHandler, $name], $parameters);
    }
}
