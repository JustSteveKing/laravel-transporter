# Transporter

[![Latest Version on Packagist](https://img.shields.io/packagist/v/juststeveking/laravel-transporter.svg?style=flat-square)](https://packagist.org/packages/JustSteveKing/laravel-transporter)
[![GitHub Tests Action Status](https://img.shields.io/github/workflow/status/JustSteveKing/laravel-transporter/run-tests?label=tests)](https://github.com/JustSteveKing/laravel-transporter/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/workflow/status/JustSteveKing/laravel-transporter/Check%20&%20fix%20styling?label=code%20style)](https://github.com/JustSteveKing/laravel-transporter/actions?query=workflow%3A"Check+%26+fix+styling"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/juststeveking/laravel-transporter.svg?style=flat-square)](https://packagist.org/packages/JustSteveKing/laravel-transporter)

Transporter is a futuristic way to send API requests in PHP. This is an OOP approach to handle API requests.


## Installation

You can install the package via composer:

```bash
composer require juststeveking/laravel-transporter
```

## Usage

```php
// Build the transporter, loading in the request you want to send
$transporter = Transporter::request(
    request: new RequestClass,
);

// Tell the transporter to send the request
$response = $transporter->send();

// You now have access to all the standard Illuminate\Http\Client\Response methods:
$response->body() : string;
$response->json() : array|mixed;
$response->collect() : Illuminate\Support\Collection;
$response->status() : int;
$response->ok() : bool;
$response->successful() : bool;
$response->failed() : bool;
$response->serverError() : bool;
$response->clientError() : bool;
$response->header($header) : string;
$response->headers() : array;
```

## Building requests

Your Request class must implement `JustSteveKing\Transporter\Contracts\RequestContract`

Here is an example, using the attached traits you can keep these as minimal as you need to:

```php
use JustSteveKing\Transporter\Contracts\RequestContract;

class TestRequest implements RequestContract
{
    use HandlesUri;
    use HasPayload;
    use HasHeaders;
    use ForwardsRequests;
    use HandlesClientSetup;
    use HandlesAuthentication;

    public string $path = 'your-resource-path';

    public string $baseUri = 'your-base-url';

    public function method(): string
    {
        return 'GET';
    }
}
```

## Alternative ways to send requests

You can build and send a request, providing overrides all in one go:

```php
$user = User::find(1);

$response = Transporter::request(
    request: new TestRequest(),
)->with(
    payload: [
        'foo' => 'bar',
    ],
    headers: [
        'User-Agent' => 'some-custom-user-agent',
    ],
    path: "users/{$user->reference_id}/posts"
)->send();
```

Alternatively you can call this directly from the Request itself:

```php
$user = User::find(1);

$response = TestRequest::with(
    payload: [
        'foo' => 'bar',
    ],
    headers: [
        'User-Agent' => 'some-custom-user-agent',
    ],
    path: "users/{$user->reference_id}/posts"
)->send();
```

Or you could implement the entire interface yourself.

## API

Lets walk through the available methods real quick:

```php
public function retry(): int;
```

This is how many times you want the request to retry if it fails


```php
public function retryTiming(): float;
```

How long do you want to leave it between retries


```php
public function timeout(): float;
```

How long do you want to leave it before timing out on a request


```php
public function requiresAuth(): bool;
```

Does this request require any sort of authentication?


```php
public function authStrategy(): string|null;
```

Which strategy do you want to use for authentication?

- 'basic'
- 'digest'
- 'token'
- null


```php
public function authCredentials(): string|array|null;
```

For token based auth, just return a string. If you are returning basic or digest make sure that you return it like the following otherwise it will fail.

```php
return [
    'your-username',
    'your-password',
];
```


```php
public function method(): string;
```

Which request method do you want to use?


```php
public function headers(array $headers = []): array;
```

What default headers needs to be sent with this request (other than authentication), these can be merged later on if required.


```php
public function payload(array $payload = []): array;
```

This should _always_ be an `array`. If you are working with an API and want to send a `POST|PATCH|PUT` request, or anything that requires a payload, ensure your payload it encapsulated in `body`, I recommend the following:

```php
$body = array_merge($payload, [
    'your' => 'payload'
]);

return [
    'body' => $body
];
```


```php
public function uri(): Uri;
```

This is your chance to build up the request URI as you require it, the only thing you need to ensure is that it returns an instance of `JustSteveKing\UriBuilder\Uri` which will give you a very flexible approach to building URIs and working with Query Parameters.


```php
public function path(string|null $path = null): null|string;
```

This is the endpoint you want to call, you can set this as a property on the class, or override it.


```php
public function parameters(array $parameters = []): array;
```

An array of default query parameters, this will allow you to override and merge the default.


```php
public function fragment(): null|string;
```

Do you need to add a fragment onto the end of your URI? These are formatted like: `/path/to/resource#fragment`.


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
