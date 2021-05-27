<?php

declare(strict_types=1);

namespace JustSteveKing\Transporter;

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
}
