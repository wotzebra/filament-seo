<?php

namespace Wotz\Seo;

use Illuminate\Support\Collection;
use Wotz\Seo\Tags\BaseTag;

class SeoTags extends Collection
{
    public function firstForTypeAndKey(string $type, string $key)
    {
        return $this->first(
            fn (BaseTag $tag) => $tag::class === $type && $tag->getKey() === $key
        );
    }
}
