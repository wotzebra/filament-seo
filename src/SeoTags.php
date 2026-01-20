<?php

namespace Wotz\Seo;

use Wotz\Seo\Tags\BaseTag;
use Illuminate\Support\Collection;

class SeoTags extends Collection
{
    public function firstForTypeAndKey(string $type, string $key)
    {
        return $this->first(
            fn (BaseTag $tag) => $tag::class === $type && $tag->getKey() === $key
        );
    }
}
