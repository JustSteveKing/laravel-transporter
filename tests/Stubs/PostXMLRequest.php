<?php

declare(strict_types=1);

namespace JustSteveKing\Transporter\Tests\Stubs;

use JustSteveKing\Transporter\Concerns\SendsXml;
use JustSteveKing\Transporter\Request;

class PostXMLRequest extends Request
{
    use SendsXml;

    protected string $method = 'POST';

    protected string $baseUrl = 'https://reqbin.com';

    protected string $path = '/echo/post/xml';

    protected string $fakeXML = <<<'XML'
        <Request>
            <Login>login</Login>
            <Password>password</Password>
        </Request>
    XML;
}
