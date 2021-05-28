# Transporter

[![Latest Version on Packagist](https://img.shields.io/packagist/v/juststeveking/laravel-transporter.svg?style=flat-square)](https://packagist.org/packages/juststeveking/laravel-transporter)
[![GitHub Tests Action Status](https://img.shields.io/github/workflow/status/JustSteveKing/laravel-transporter/run-tests?label=tests)](https://github.com/JustSteveKing/laravel-transporter/actions?query=workflow%3Arun-tests+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/juststeveking/laravel-transporter.svg?style=flat-square)](https://packagist.org/packages/juststeveking/laravel-transporter)

Transporter is a futuristic way to send API requests in PHP. This is an OOP approach to handle API requests.

<p align="center">

![](banner.png)

</p>

**This package is still a work in progress**


## Installation

You can install the package via composer:

```bash
composer require juststeveking/laravel-transporter
```

## Generating Request

To generate an API request to use with Transporter, you can use the Artisan make command:

```bash
php artisan make:api-request NameOfYourRequest
```

This will by default publish as: `app/Transporter/Requests/NameOfYourRequest.php`


## Usage

Transporter Requests are an extention of Laravels `PendingRequest` so all of the methods available on a Pending Request is available to you on your requests.

Also when you send the request, you will receive a `Illuminate\Http\Client\Response` back, allowing you to do things such as `collect($key)` and `json()` and `failed()` very easily. We are simply just shifting how we send it into a class based approach.

```php
TestRequest::build()
    ->withToken('foobar')
    ->withData([
        'title' => 'Build a package'
    ])
    ->send()
    ->json();
```

When building your request to send, you can override the following:

- Request Data using `withData(aray $data)`
- Request Query Params using `withQuery(array $query)`
- Request Path using `setPath(string $path)`


## Testing

To run the tests in parallel:

```bash
composer run test
```

To run the tests with a coverage report:

```bash
composer run test-coverage
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](.github/CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Zuzana Kunckova](https://github.com/zuzana-kunckova)
- [Steve McDougall](https://github.com/JustSteveKing)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
