<?php

declare(strict_types=1);

namespace JustSteveKing\Transporter\Concerns;

use GuzzleHttp\Psr7\Response as Psr7Response;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;

trait SendsXml
{
    protected string $fakeXml = '';

    protected string $xml;

    public function fakeResponse(): Psr7Response
    {
        return new Psr7Response(
            status: $this->status,
            body: $this->fakeXml,
        );
    }

    public function send(): Response
    {
        if ($this->useFake) {
            return new Response(
                response: $this->fakeResponse(),
            );
        }

        $this->ensureRequest();

        return $this->request->send(
            method: $this->method,
            url: $this->getUrl(),
        );
    }

    public function withFakeXml(string $xml): static
    {
        $this->fakeXml = $xml;

        return $this;
    }

    public function withXml(string $xml): static
    {
        $this->xml = $xml;

        return $this;
    }

    protected function withRequest(PendingRequest $request): void
    {
        $this->request
            ->withHeaders(
                headers: [
                    'Accept' => 'application/xml',
                ],
            )->withBody(
                content: $this->xml,
                contentType: 'application/xml',
            );
    }
}
