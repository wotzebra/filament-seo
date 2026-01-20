<?php

namespace Wotz\Seo\Tags;

class OgUrl extends OpenGraph
{
    protected string $key = 'url';

    public function getContent(bool $raw = false): string
    {
        return request()->fullUrl();
    }
}
