# Exception Handler

[![Author][ico-author]][link-author]
[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE.md)
[![Build Status][ico-travis]][link-travis]
[![Coverage Status][ico-scrutinizer]][link-scrutinizer]
[![Quality Score][ico-code-quality]][link-code-quality]
[![Total Downloads][ico-downloads]][link-downloads]

[![SensioLabsInsight][ico-sensiolabs]][link-sensiolabs]

This Laravel package lets you define the behavior of your application when a specific exception is thrown.

Laravel handles exceptions in `app/Exceptions/Handler.php` by default, but the more handlers you add the more this class gets cluttered and difficult to read and maintain.

Furthermore it is not possible for an external Laravel package to automatically register how its custom exceptions should be handled by the application where it has been installed.

This package lets you register custom exception handlers by using service providers, so that also external packages may register their own.

> **Please note:** this package leverages the decorators design pattern, which allows you to keep using the Laravel default handler as you normally would. It just wraps the exception handler to extend its functionalities.

## Install

Via Composer

``` bash
composer require cerbero/exception-handler
```

If your Laravel version is prior to 5.5, please add this service provider in `config/app.php`

``` php
'providers' => [
    ...
    Cerbero\ExceptionHandler\Providers\ExceptionHandlerServiceProvider::class,
]
```

## Usage

There are 3 types of handlers that can be registered:

 - **Reporters**, they log an exception or report it to an external service (e.g. Sentry, Bugsnag...)
 - **Renderers**, they render an exception into an HTTP response
 - **Console Renderers**, they render an exception to the console

The following examples show how to register each type of handler in the `boot()` method of a service provider:

``` php
use App\Exceptions\DebugException;
use App\Exceptions\ArtisanException;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Contracts\Validation\ValidationException;

...

public function boot()
{
    // register a custom reporter to log all exceptions that are instances of - or extend - DebugException
    $this->app->make(ExceptionHandler::class)->reporter(function (DebugException $e) {
        $this->app['log']->debug($e->getMessage());
    });

    // register a custom renderer to redirect the user back and show validation errors
    $this->app->make(ExceptionHandler::class)->renderer(function (ValidationException $e, $request) {
        return back()->withInput()->withErrors($e->errors());
    });

    // register a custom console renderer to display errors to the console and stop the propagation of other exceptions
    $this->app->make(ExceptionHandler::class)->consoleRenderer(function (ArtisanException $e, $output) {
        $output->writeln('<error>' . $e->getMessage() . '</error>');
        return true;
    });
}
```

A handler is basically a callback that accepts the exception to handle as first parameter. You can register a global handler by omitting the exception type or type-hinting `Exception`.

Unlike reporters, renderers also accept a second parameter: an instance of `Illuminate\Http\Request` in case of a renderer or a `Symfony\Component\Console\Output\OutputInterface` in case of a console renderer.

It is also important to note that all registered handlers for an exception will be called until one of them returns a truthy value, in that case the exceptions propagation stops.

## Change log

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Testing

``` bash
composer test
```

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) and [CONDUCT](CONDUCT.md) for details.

## Security

If you discover any security related issues, please email andrea.marco.sartori@gmail.com instead of using the issue tracker.

## Credits

- [Andrea Marco Sartori][link-author]
- [All Contributors][link-contributors]

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

[ico-author]: http://img.shields.io/badge/author-@cerbero90-blue.svg?style=flat-square
[ico-version]: https://img.shields.io/packagist/v/cerbero/exception-handler.svg?style=flat-square
[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square
[ico-travis]: https://img.shields.io/travis/cerbero90/exception-handler/master.svg?style=flat-square
[ico-scrutinizer]: https://img.shields.io/scrutinizer/coverage/g/cerbero90/exception-handler.svg?style=flat-square
[ico-code-quality]: https://img.shields.io/scrutinizer/g/cerbero90/exception-handler.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/cerbero/exception-handler.svg?style=flat-square
[ico-sensiolabs]: https://insight.sensiolabs.com/projects/696d2f5d-ea7e-484a-8e06-836dcb462b19/big.png

[link-author]: https://twitter.com/cerbero90
[link-packagist]: https://packagist.org/packages/cerbero/exception-handler
[link-travis]: https://travis-ci.org/cerbero90/exception-handler
[link-scrutinizer]: https://scrutinizer-ci.com/g/cerbero90/exception-handler/code-structure
[link-code-quality]: https://scrutinizer-ci.com/g/cerbero90/exception-handler
[link-downloads]: https://packagist.org/packages/cerbero/exception-handler
[link-sensiolabs]: https://insight.sensiolabs.com/projects/696d2f5d-ea7e-484a-8e06-836dcb462b19
[link-contributors]: ../../contributors
