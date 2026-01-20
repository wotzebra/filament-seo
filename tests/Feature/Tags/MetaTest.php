<?php

use Wotz\Seo\Tags\Meta;
use Wotz\Seo\Tags\Tag;

it('can construct a class', function () {
    expect(new Meta(new \Wotz\Seo\Tests\Fixtures\Models\Page, 'key', 'content'))
        ->toBeInstanceOf(Tag::class);
});

it('has a meta identifier prefix', function () {
    expect(new Meta(new \Wotz\Seo\Tests\Fixtures\Models\Page, 'key', 'content'))
        ->getIdentifier()->toBe('meta_key');
});
