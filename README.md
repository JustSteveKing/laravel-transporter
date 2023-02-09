# Transporter

[![Latest Version on Packagist](https://img.shields.io/packagist/v/juststeveking/laravel-transporter.svg?style=flat-square)](https://packagist.org/packages/juststeveking/laravel-transporter)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/JustSteveKing/laravel-transporter/run-tests.yml?branch=main&label=Tests&style=flat-square)](https://github.com/JustSteveKing/transporter/actions?query=workflow%3Arun-tests+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/juststeveking/laravel-transporter.svg?style=flat-square)](https://packagist.org/packages/juststeveking/laravel-transporter)

Transporter is a futuristic way to send API requests in PHP. This is an OOP approach to handle API requests.

<p align="center">

![](banner.png)

</p>

## Installation

You can install the package via composer:

```bash
composer require juststeveking/laravel-transporter
```

You can publish the config file with:

```bash
php artisan vendor:publish --provider="JustSteveKing\Transporter\TransporterServiceProvider" --tag="transporter-config"
```

The contents of the published config file:

```php
return [
    'base_uri' => env('TRANSPORTER_BASE_URI'),
];
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

- Request Data using `withData(array $data)`
- Request Query Params using `withQuery(array $query)`
- Request Path using `setPath(string $path)`


### Checking the payload

I had a request in an issue to be able to see the request data for a request, so I have added a helper method called `payload` which will return whatever has been stored in the request `data` property.

```php
$request = TestRequest::build()
    ->withToken('foobar')
    ->withData([
        'title' => 'Build a package'
    ]);

$data = $request->payload(); // ['title' => 'Build a package']
```


### Concurrent Requests

```php
$responses = \JustSteveKing\Transporter\Facades\Concurrently::build()->setRequests([
    TestRequest::build()
        ->withToken('foobar')
        ->withData([
        'title' => 'Build a package'
    ]),
    TestRequest::build()
        ->withToken('foobar')
        ->withData([
        'title' => 'Build a package'
    ]),
    TestRequest::build()
        ->withToken('foobar')
        ->withData([
        'title' => 'Build a package'
    ]),
]);

$responses[0]->json();
$responses[1]->json();
$responses[2]->json();
```

### Concurrency with a Custom key

```php
$responses = \JustSteveKing\Transporter\Facades\Concurrently::build()->setRequests([
    TestRequest::build()
        ->as(
            key: 'first'
        )
        ->withToken('foobar')
        ->withData([
        'title' => 'Build a package'
    ]),
    TestRequest::build()
        ->as(
            key: 'second'
        )
        ->withToken('foobar')
        ->withData([
        'title' => 'Build a package'
    ]),
    TestRequest::build()
        ->as(
            key: 'third'
        )
        ->withToken('foobar')
        ->withData([
        'title' => 'Build a package'
    ]),
]);

$responses['first']->json();
$responses['second']->json();
$responses['third']->json();
```

### Optional Alias

Instead of the standard `send()` method, it is also possible to use the fun alias `energize()`. *Please note, no sound effects are included.*

```php
TestRequest::build()
    ->withToken('foobar')
    ->withData([
        'title' => 'Build a package'
    ])
    ->energize()
    ->json();
```

### Faking a Request or Concurrent

To fake a request, all you need to do is replace the build method with the fake method, which takes an optional `status` parameter, to set the status code being returned with the response:

```php
TestRequest::fake(
    status: 200,
)->withToken('foobar')
->withData([
    'title' => 'Build a package'
])->withFakeData([
    'data' => 'faked'
])->send();
```

```php
$responses = Concurrently::fake()->setRequests([
    TestRequest::fake()->setPath(
        path: '/todos/1',
    )->as(
        key: 'first'
    ),
    TestRequest::fake()->setPath(
        path: '/todos/2',
    )->as(
        key: 'second'
    ),
    TestRequest::fake()->setPath(
        path: '/todos/3',
    )->as(
        key: 'thirds'
    ),
])->run();
```

Which will return a response with the data you pass through to `withFakeData`, which internally will merge what is on the class with what you pass it. So you can build up an initial state of faked data per class.

### Sending XML

Thanks to a fantastic suggestion by [@jessarcher](https://github.com/jessarcher) we can use a `Trait` to allow for easy use of XML in your requests. Using this as a trait makes a lot of sense as most APIs these days use JSON, so it is purely opt in. 
To use this, simply use the trait on your request:

```php
<?php

declare(strict_types=1);

namespace App\Transporter\Requests;

use JustSteveKing\Transporter\Concerns\SendsXml;
use JustSteveKing\Transporter\Request;

class XmlRequest extends Request
{
    use SendsXml;
    
    protected string $method = 'POST';
    
    protected string $path = '/your-endpoint';
}
```
Then all you need to do is call the methods:

```php
XmlRequest::build()->withXml(
    xml: '<todo><name>Send an XML Requets</name><completed>false</completed></todo>'
)->send();
```

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
