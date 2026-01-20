<?php

use Wotz\Seo\Tags\OgUrl;
use Wotz\Seo\Tags\Tag;

it('can construct a class', function () {
    expect(new OgUrl(new \Wotz\Seo\Tests\Fixtures\Models\Page, 'key', 'content'))
        ->toBeInstanceOf(Tag::class);
});

it('has a meta identifier prefix', function () {
    expect(new OgUrl(new \Wotz\Seo\Tests\Fixtures\Models\Page, 'key', 'content'))
        ->getContent()->toBe('http://localhost');
});
