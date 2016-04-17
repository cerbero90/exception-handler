# Exception Handler

[![Author][ico-author]][link-author]
[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE.md)
[![Build Status][ico-travis]][link-travis]
[![Coverage Status][ico-scrutinizer]][link-scrutinizer]
[![Quality Score][ico-code-quality]][link-code-quality]
[![StyleCI][ico-styleci]][link-styleci]
[![Total Downloads][ico-downloads]][link-downloads]
[![Gratipay][ico-gratipay]][link-gratipay]

[![SensioLabsInsight][ico-sensiolabs]][link-sensiolabs]

Custom exception handlers let you define the behavior of your application when a specific exception is thrown.

By default you can add your custom handlers in `app/Exceptions/Handler.php`, but the more handlers you add
the more this class gets cluttered and difficult to read.

Furthermore it is not possible for an external Laravel package to automatically register how its
custom exceptions should be handled by the application where it is being installed.

This is where this package comes in handy, it lets you register your custom exception handlers by using
service providers, so that also external packages may register their own.

> **Please note:** this package leverages the decorators design pattern, which let you keep using the
> Laravel default handler as you normally would. It just wraps the Handler class to extend its functionalities.

## Install

Install this package via Composer

``` bash
composer require cerbero/exception-handler
```

And then add the service provider in `config/app.php`

``` php
'providers' => [
    Cerbero\ExceptionHandler\ServiceProvider::class,
]
```

## Usage

There are 3 types of handlers that can be registered:

 - **Reporters**, they log an exception or report it to an external service (Sentry, Bugsnag...)
 - **Renderers**, they render an exception into an HTTP response
 - **Console Renderers**, they render an exception to the console

The following examples show how to register each type of handler, you can use both the service container and
the `Exceptions` facade, just make sure to register your handlers in the `boot()` method of a service provider.

``` php
use Exceptions;
use App\Exceptions\DebugException;
use App\Exceptions\ArtisanException;
use Illuminate\Contracts\Validation\ValidationException;

...

public function boot()
{
    // register a custom reporter to log all exceptions that are (or extend) DebugException
    $this->app['exceptions']->reporter(function (DebugException $e) {
        $this->app['log']->debug($e->getMessage());
    });

    // register a custom renderer to redirect the user back and show validation errors
    Exceptions::renderer(function (ValidationException $e, $request) {
        return back()->withInput()->withErrors($e->errors());
    });

    // register a custom console renderer to display errors to the console
    app('exceptions')->consoleRenderer(function (ArtisanException $e, $output) {
        $output->writeln('<error>' . $e->getMessage() . '</error>');
        return true;
    });
}
```

A handler is basically a Closure that accepts the exception to handle as first parameter.
You can register a global handler by omitting the exception type or by just specifying `Exception`.

Despite reporters, renderers also accept a second parameter: the current instance of `Illuminate\Http\Request`
is passed to renderers, as `Symfony\Component\Console\Output\OutputInterface` is passed to console renderers.

It is also important to note that all the registered handlers of an exception will be called until one of
them returns a truthy value. When a handler returns any non-falsy value the exception propagation stops.

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
[ico-styleci]: https://styleci.io/repos/56225627/shield
[ico-downloads]: https://img.shields.io/packagist/dt/cerbero/exception-handler.svg?style=flat-square
[ico-gratipay]: https://img.shields.io/gratipay/cerbero.svg?style=flat-square
[ico-sensiolabs]: https://insight.sensiolabs.com/projects/696d2f5d-ea7e-484a-8e06-836dcb462b19/big.png

[link-author]: https://twitter.com/cerbero90
[link-packagist]: https://packagist.org/packages/cerbero/exception-handler
[link-travis]: https://travis-ci.org/cerbero90/exception-handler
[link-scrutinizer]: https://scrutinizer-ci.com/g/cerbero90/exception-handler/code-structure
[link-code-quality]: https://scrutinizer-ci.com/g/cerbero90/exception-handler
[link-styleci]: https://styleci.io/repos/56225627
[link-downloads]: https://packagist.org/packages/cerbero/exception-handler
[link-gratipay]: https://gratipay.com/cerbero
[link-sensiolabs]: https://insight.sensiolabs.com/projects/696d2f5d-ea7e-484a-8e06-836dcb462b19
[link-contributors]: ../../contributors
