<?php

namespace JustSteveKing\Transporter;

use Illuminate\Http\Client\Pool;
use Illuminate\Http\Client\Response;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Factory as HttpFactory;
use Illuminate\Support\Collection;

class Concurrently
{

    /**
     * Requests holder.
     *
     * @var array|\JustSteveKing\Transporter\Request[]
     */
    protected array $requests = [];

    protected bool $isFake = false;

    /**
     * @param HttpFactory $http
     * @return void
     */
    public function __construct(
        private HttpFactory $http
    ) { }

    /**
     * @return static
     */
    public function build(): static
    {
        return app(static::class);
    }

    /**
     * @return static
     */
    public function fake(): static
    {
        $concurrent = $this->build();

        $concurrent->isFake = true;

        return $concurrent;
    }

    /**
     * @param array $requests
     * @return static
     */
    public function setRequests(array $requests): static
    {
        $this->requests = $requests;
        foreach ($requests as $request) {
            if ($request->getAs() !== null) {
                $this->requests[$request->getAs()] = $request;
            } else {
                $this->requests[] = $request;
            }
        }

        return $this;
    }

    /**
     * @param Request $request
     * @return static
     */
    public function add(Request $request): static
    {
        if ($request->getAs() !== null) {
            $this->requests[$request->getAs()] = $request;
        } else {
            $this->requests[] = $request;
        }

        return $this;
    }

    /**
     * @return array
     */
    public function run(): array
    {
        if ($this->isFake) {
            $this->http->fake();
        }

        return collect($this->http->pool(fn(Pool $pool) => $this->buildRequestsPool($pool)))
            ->mapWithKeys(fn(Response $response, string|int $key) => [$key => $this->isFake ? new Response(response: $this->requests[$key]->fakeResponse()) : $response])
            ->toArray();
    }

    /**
     * @param $pool
     * @return array
     */
    private function buildRequestsPool($pool): array
    {
        $requests = [];
        foreach ($this->requests as $request) {
            $requests[] = $request->buildForConcurrent($pool);
        }
        return $requests;
    }
}
