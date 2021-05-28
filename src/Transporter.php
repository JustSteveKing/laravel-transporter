<?php

declare(strict_types=1);

namespace JustSteveKing\Transporter;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;

class Transporter
{
    public function __construct(
        public Collection $requests,
    ) {}

    public static function request(
        ...$request,
    ): self {
        return new self(
            requests: collect($request),
        );
    }

    public function attach(Request $request): self
    {
        $this->requests->add($request);

        return $this;
    }

    public function dispatch(): array
    {
        return Http::pool(fn () => $this->requests->map(fn ($request) => $request->send()));
    }
}
