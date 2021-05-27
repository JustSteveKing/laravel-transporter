<?php

declare(strict_types=1);

namespace JustSteveKing\Transporter\Concerns;

use JustSteveKing\UriBuilder\Uri;

trait HandlesUri
{
    public function uri(): Uri
    {
        $uri = Uri::fromString(
            uri: $this->baseUri,
        );

        if (! empty($this->path())) {
            $uri->addPath(
                path: $this->path(),
            );
        }

        if (! empty($this->parameters())) {
            foreach ($this->parameters() as $key => $value) {
                $uri->addQueryParam(
                    key: $key,
                    value: $value,
                    covertBoolToString: true,
                );
            }
        }

        if (! empty($this->fragment())) {
            $uri->addFragment(
                fragment: $this->fragment(),
            );
        }

        return $uri;
    }

    public function path(string|null $path = null): null|string
    {
        return $path ?? $this->path;
    }

    public function parameters(array $parameters = []): array
    {
        return array_merge($parameters, []);
    }

    public function fragment(): null|string
    {
        return null;
    }
}
