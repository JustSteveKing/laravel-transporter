<?php

declare(strict_types=1);

namespace JustSteveKing\Transporter;

use OutOfBoundsException;
use BadMethodCallException;
use Illuminate\Support\Str;
use Illuminate\Support\Stringable;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Traits\Macroable;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Factory as HttpFactory;

abstract class Request
{
    use Macroable {
        __call as macroCall;
    }
    
    protected PendingRequest $request;

    protected string $method;
    protected string $path;
    protected string $baseUrl;

    protected array $query = [];
    protected array $data = [];

    public static function for(...$args): self
    {
        return app(static::class, $args);
    }

    public function __construct(HttpFactory $http)
    {
        $this->request = $http->baseUrl($this->baseUrl);

        $this->withRequest($this->request);
    }

    public function withData(array $data): self
    {
        $this->data = array_merge($this->data, $data);

        return $this;
    }

    public function withQuery(array $query): self
    {
        $this->query = array_merge($this->query, $query);

        return $this;
    }

    public function send(): Response
    {
        $url = (string) Str::of($this->path())
            ->when(
                !empty($this->query),
                fn(Stringable $path): Stringable => $path->append('?', http_build_query($this->query))
            );

        switch (mb_strtoupper($this->method)) {
            case 'GET':
                return $this->request->get($this->path(), $this->query);
            case 'POST':
                return $this->request->post($url, $this->data);
            case 'PUT':
                return $this->request->put($url, $this->data);
            case 'PATCH':
                return $this->request->patch($url, $this->data);
            case 'DELETE':
                return $this->request->delete($url, $this->data);
            case 'HEAD':
                return $this->request->head($this->path(), $this->query);
            default:
                throw new OutOfBoundsException();
        }
    }

    protected function withRequest(PendingRequest $request): void
    {
        // do something with the initialized request
    }

    protected function path(): string
    {
        return $this->path;
    }

    public function __call(string $name, array $arguments): self
    {
        if (method_exists($this->request, $name)) {
            call_user_func_array([$this->request, $name], $arguments);

            return $this;
        }

        throw new BadMethodCallException();
    }
}
