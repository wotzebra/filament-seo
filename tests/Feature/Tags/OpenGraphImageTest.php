<?php

use Wotz\Seo\Tags\OpenGraphImage;
use Wotz\Seo\Tags\Tag;

it('can construct a class', function () {
    expect(new OpenGraphImage(new \Wotz\Seo\Tests\Fixtures\Models\Page, 'key', 'content'))
        ->toBeInstanceOf(Tag::class);
});

it('will return content as is in the beforeSave', function () {
    $content = fake()->realTextBetween(280, 300);

    expect(new OpenGraphImage(new \Wotz\Seo\Tests\Fixtures\Models\Page, 'key', $content))
        ->beforeSave($content)->toBe($content);
});

it('returns empty string if content is null', function () {
    expect(new OpenGraphImage(new \Wotz\Seo\Tests\Fixtures\Models\Page, 'key', null))
        ->getContent()->toBe('');
});

it('can return the raw content', function () {
    expect(new OpenGraphImage(new \Wotz\Seo\Tests\Fixtures\Models\Page, 'key', 'content'))
        ->getContent(true)->toBe('');
});

it('can return the content', function () {
    expect(new OpenGraphImage(new \Wotz\Seo\Tests\Fixtures\Models\Page, 'key', 'content'))
        ->getContent()->toBe('');
});
