<?php

declare(strict_types=1);

namespace JustSteveKing\Transporter\Concerns;

use Carbon\CarbonInterval;

trait HandlesClientSetup
{
    public function retry(): int
    {
        return 3;
    }

    public function retryTiming(): float
    {
        return CarbonInterval::milliseconds(
            milliseconds: 300,
        )->totalMicroseconds;
    }

    public function timeout(): float
    {
        return CarbonInterval::seconds(
            seconds: 10,
        )->totalSeconds;
    }
}
