<?php

namespace Wotz\Seo\Facades;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Facade;

/**
 * @method static void tag(\Wotz\Seo\Tags\Tag $item)
 * @method static void tags(array $items, bool $overwrite = true)
 * @method static string render()
 * @method static Collection contents()
 * @method static object getTags()
 * @method static string getTag(string $tagName)
 *
 * @see \Wotz\Seo\SeoBuilder
 */
class SeoBuilder extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'seo';
    }
}
