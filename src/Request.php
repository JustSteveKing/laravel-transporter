<?php

declare(strict_types=1);

namespace JustSteveKing\Transporter;

use GuzzleHttp\Promise\Promise;
use Illuminate\Http\Client\Pool;
use JustSteveKing\StatusCode\Http;
use OutOfBoundsException;
use BadMethodCallException;
use Illuminate\Support\Str;
use Illuminate\Support\Stringable;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Traits\Macroable;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Factory as HttpFactory;
use GuzzleHttp\Psr7\Response as Psr7Response;
use RuntimeException;

abstract class Request
{
    use Macroable {
        __call as macroCall;
    }

    protected bool $useFake = false;

    protected PendingRequest $request;

    protected Response $fakeResponse;

    protected string $method;
    protected string $path;
    protected string $baseUrl;

    protected ?string $as = null;
    protected array $query = [];
    protected array $data = [];
    protected array $fakeData = [];
    protected int $status;

    public static function build(...$args): static
    {
        return app(static::class, $args);
    }

    public static function fake(int $status = Http::OK): static
    {
        $request = static::build();

        $request->useFake = true;
        $request->status = $status;

        return $request;
    }

    public function __construct(HttpFactory $http)
    {
        $this->request = $http->baseUrl(
            url: config('transporter.base_uri') ?? $this->baseUrl ?? '',
        );

        $this->withRequest(
            request: $this->request,
        );
    }

    public function getAs(): ?string
    {
        return $this->as;
    }

    public function as(string|int $as): static
    {
        $this->as = $as;

        return $this;
    }

    public function withData(array $data): static
    {
        $this->data = array_merge($this->data, $data);

        return $this;
    }

    public function withFakeData(array $data): static
    {
        $this->fakeData = array_merge($this->fakeData, $data);

        return $this;
    }

    public function withQuery(array $query): static
    {
        $this->query = array_merge_recursive($this->query, $query);

        return $this;
    }

    public function getBaseUrl(): string
    {
        if (isset($this->baseUrl)) {
            return $this->baseUrl;
        }

        if (! is_null(config('transporter.base_uri'))) {
            return config('transporter.base_uri');
        }

        throw new RuntimeException(
            message: "Neither a baseUrl or a config base_uri has been set for this request.",
        );
    }

    public function lockOn(string $baseUrl): static
    {
        return $this->setBaseUrl($baseUrl);
    }

    public function setBaseUrl(string $baseUrl): static
    {
        $this->baseUrl = $baseUrl;

        $this->request->baseUrl($baseUrl);

        return $this;
    }

    public function fakeResponse(): Psr7Response
    {
        return new Psr7Response(
            status: $this->status,
            body:   json_encode($this->fakeData),
        );
    }

    public function buildForConcurrent(Pool $pool): mixed
    {
        /**
         * @var $poolItem Pool
         */
        $poolItem = match ($this->as === null) {
            false => $pool->as(
                key: $this->as
            ),
            true => $pool
        };

        return match (strtoupper($this->method)) {
            "GET" => $poolItem->get($this->getUrl(), $this->query),
            "POST" => $poolItem->post($this->getUrl(), $this->data),
            "PUT" => $poolItem->put($this->getUrl(), $this->data),
            "PATCH" => $poolItem->patch($this->getUrl(), $this->data),
            "DELETE" => $poolItem->delete($this->getUrl(), $this->data),
            "HEAD" => $poolItem->head($this->getUrl(), $this->query),
            default => throw new OutOfBoundsException()
        };
    }

    public function energize(): Response
    {
        return $this->send();
    }

    public function send(): Response
    {
        if ($this->useFake) {
            return new Response(
                response: $this->fakeResponse(),
            );
        }

        return match (mb_strtoupper($this->method)) {
            "GET" => $this->request->get($this->getUrl(), $this->query),
            "POST" => $this->request->post($this->getUrl(), $this->data),
            "PUT" => $this->request->put($this->getUrl(), $this->data),
            "PATCH" => $this->request->patch($this->getUrl(), $this->data),
            "DELETE" => $this->request->delete($this->getUrl(), $this->data),
            "HEAD" => $this->request->head($this->getUrl(), $this->query),
            default => throw new OutOfBoundsException()
        };
    }

    public function getUrl(): string
    {
        $url = (string) Str::of($this->path())
                           ->when(
                               !empty($this->query),
                               fn (Stringable $path): Stringable => $path->append('?', http_build_query($this->query))
                           );
        if(Str::of($this->method)->upper()->contains('GET','HEAD')){
            return $this->path();
        }
        return $url;
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

    public function getQuery(): array
    {
        return $this->query;
    }

    public function setPath(string $path): static
    {
        $this->path = $path;

        return $this;
    }

    public function __call(string $method, array $parameters): static
    {
        if (method_exists($this->request, $method)) {
            call_user_func_array([$this->request, $method], $parameters);

            return $this;
        }

        throw new BadMethodCallException();
    }
}
