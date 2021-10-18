<?php

namespace JustSteveKing\Transporter\Facades;

use Illuminate\Support\Facades\Facade;
use JustSteveKing\Transporter\Request;

/**
 * Class Pool
 *
 * @method static \JustSteveKing\Transporter\Concurrently build()
 * @method static \JustSteveKing\Transporter\Concurrently fake()
 * @method static \JustSteveKing\Transporter\Concurrently setRequests(Request[] $requests)
 * @method static \JustSteveKing\Transporter\Concurrently add(Request $request)
 * @method static \Illuminate\Support\Collection run()
 *
 * @package JustSteveKing\Transporter\Facades
 *
 * @date    18/10/2021
 */
class Concurrently extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \JustSteveKing\Transporter\Concurrently::class;
    }
}
