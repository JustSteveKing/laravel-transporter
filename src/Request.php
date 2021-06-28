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
use GuzzleHttp\Psr7\Response as Psr7Response;

abstract class Request
{
    use Macroable {
        __call as macroCall;
    }

    protected static bool $useFake = false;
    
    protected PendingRequest $request;

    protected Response $fakeResponse;

    protected string $method;
    protected string $path;
    protected string $baseUrl;

    protected array $query = [];
    protected array $data = [];

    public static function build(...$args): static
    {
        return app(static::class, $args);
    }

    public static function fake(): void
    {
        static::$useFake = true;
    }

    public function __construct(HttpFactory $http)
    {
        $this->request = $http->baseUrl($this->baseUrl);

        $this->withRequest($this->request);
    }

    public function withData(array $data): static
    {
        $this->data = array_merge($this->data, $data);

        return $this;
    }

    public function withQuery(array $query): static
    {
        $this->query = array_merge($this->query, $query);

        return $this;
    }

    public function send(): Response
    {
        if (static::$useFake) {
            if (method_exists($this, "fakeResponse")) {
                return new Response($this->fakeResponse($this->request));
            }
            return new Response(new Psr7Response());
        }
        
        $url = (string) Str::of($this->path())
            ->when(
                !empty($this->query),
                fn (Stringable $path): Stringable => $path->append('?', http_build_query($this->query))
            );

        return match (mb_strtoupper($this->method)) {
            "GET" => $this->request->get($this->path(), $this->query),
            "POST" => $this->request->post($url, $this->data),
            "PUT" => $this->request->put($url, $this->data),
            "PATCH" => $this->request->patch($url, $this->data),
            "DELETE" => $this->request->delete($url, $this->data),
            "HEAD" => $this->request->head($this->path(), $this->query),
            default => throw new OutOfBoundsException()
        };
    }

    protected function withRequest(PendingRequest $request): void
    {
        // do something with the initialized request
    }

    protected function path(): string
    {
        return $this->path;
    }

    public function getRequest(): PendingRequest
    {
        return $this->request;
    }

    public function setPath(string $path): static
    {
        $this->path = $path;

        return $this;
    }

    public function __call(string $name, array $arguments): static
    {
        if (method_exists($this->request, $name)) {
            call_user_func_array([$this->request, $name], $arguments);

            return $this;
        }

        throw new BadMethodCallException();
    }
}
