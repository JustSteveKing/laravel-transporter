<?php

declare(strict_types=1);

namespace JustSteveKing\Transporter\Concerns;

use Illuminate\Http\Client\Response;

trait SendsXml
{
    /**
     * @param string $xml
     * @return static
     */
    public function withXml(string $xml): static
    {
        $this->request
            ->withHeaders(
                headers: [
                    'Accept' => 'application/xml',
                ],
            )->withBody(
                content: $xml,
                contentType: 'application/xml',
            );

        return $this;
    }

    /**
     * @return Response
     */
    public function send(): Response
    {
        return $this->request->send(
            method: $this->method,
            url: $this->getUrl(),
        );
    }
}
