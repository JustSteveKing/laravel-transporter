<?php

declare(strict_types=1);

namespace JustSteveKing\Transporter\Concerns;

trait HandlesClientSetup
{
    public function retry(): int
    {
        return 3;
    }

    public function retryTiming(): float
    {
        return 300;
    }

    public function timeout(): float
    {
        return 10;
    }
}
