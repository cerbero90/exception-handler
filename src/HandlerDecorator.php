<?php

namespace Cerbero\ExceptionHandler;

use Exception;
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
     * @param \Exception $e
     * @return mixed
     *
     * @throws \Exception
     */
    public function report(Exception $e)
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
     * @param \Exception $e
     * @return \Illuminate\Http\Response|\Symfony\Component\HttpFoundation\Response
     */
    public function render($request, Exception $e)
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
     * @param \Exception $e
     * @return mixed
     */
    public function renderForConsole($output, Exception $e)
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
     * @param \Exception $e
     * @return bool
     */
    public function shouldReport(Exception $e)
    {
        if (method_exists($this->defaultHandler, 'shouldReport')) {
            return $this->defaultHandler->shouldReport($e);
        }

        return true;
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
