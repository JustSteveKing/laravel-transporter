<?php

declare(strict_types=1);

namespace JustSteveKing\Transporter\Tests\Stubs;

use JustSteveKing\Transporter\Concerns\SendsXml;
use JustSteveKing\Transporter\Request;

class PostXMLRequest extends Request
{
    use SendsXml;
}
