<?php

namespace Wotz\Seo\Tags;

class OpenGraph extends BaseTag
{
    protected string $attribute = 'property';

    protected string $prefix = 'og:';

    protected string $identifierPrefix = 'og_';
}
