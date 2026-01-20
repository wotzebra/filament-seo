<?php

use Wotz\Seo\Tags\OpenGraph;
use Wotz\Seo\Tags\Tag;

it('can construct a class', function () {
    expect(new OpenGraph(new \Wotz\Seo\Tests\Fixtures\Models\Page, 'key', 'content'))
        ->toBeInstanceOf(Tag::class);
});

it('has an attribute', function () {
    expect(new OpenGraph(new \Wotz\Seo\Tests\Fixtures\Models\Page, 'key', 'content'))
        ->render()->toBe('<meta property="og:key" content="">');
});

it('has an og: prefix', function () {
    expect(new OpenGraph(new \Wotz\Seo\Tests\Fixtures\Models\Page, 'key', 'content'))
        ->getPrefixedKey()->toBe('og:key');
});

it('has an og_ identifier prefix', function () {
    expect(new OpenGraph(new \Wotz\Seo\Tests\Fixtures\Models\Page, 'key', 'content'))
        ->getIdentifier()->toBe('og_key');
});
