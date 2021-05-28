<?php

declare(strict_types=1);

namespace JustSteveKing\Transporter;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\PendingRequest;
use JustSteveKing\Transporter\Concerns\HasFake;
use JustSteveKing\Transporter\Contracts\RequestContract;

class Transporter
{
    use HasFake;

    public function __construct(
        public RequestContract $request,
    ) {}

    public static function request(
        RequestContract $request,
    ): self {
        return new self(
            request: $request,
        );
    }

    public function send(): Response
    {
        $request = $this->buildRequest();

        $response = $request->send(
            method: $this->request->method(),
            url: $this->request->uri()->toString(),
            options: $this->request->payload(),
        );

        if ($response->failed()) {
            throw $response->toException();
        }

        return $response;
    }

    public function with(
        array $payload = [],
        array $headers = [],
    ): self {
        if (! empty($payload)) {
            $this->request->payload(
                payload: $payload,
            );
        }

        if (! empty($headers)) {
            $this->request->headers(
                headers: $headers,
            );
        }

        return $this;
    }

    public function to(
        ?string $path = null,
    ): self
    {
        if (! is_null($path)) {
            $this->request->path(
                path: $path,
            );
        }

        return $this;
    }

    public function buildRequest(): PendingRequest
    {
        $client = Http::withHeaders(
            headers: $this->request->headers(),
        )->retry(
            times: (int) $this->request->retry(),
            sleep: (int) $this->request->retryTiming(),
        )->timeout(
            seconds: (int) $this->request->timeout(),
        );

        if ($this->request->requiresAuth()) {

            if ($this->request->authStrategy() === 'token') {
                [$token, $type] = $this->request->authCredentials();

                $client->withToken(
                    token: $token,
                    type: $type ?? 'Bearer',
                );
            }

            if ($this->request->authStrategy() === 'basic') {
                [$username, $password] = $this->request->authCredentials();

                $client->withBasicAuth(
                    username: $username,
                    password: $password,
                );
            }

            if ($this->request->authStrategy() === 'digest') {
                [$username, $password] = $this->request->authCredentials();

                $client->withDigestAuth(
                    username: $username,
                    password: $password,
                );
            }
        }

        return $client;
    }
}
