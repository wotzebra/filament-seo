<?php

use Wotz\Seo\SeoBuilder;
use Wotz\Seo\Tags\BaseTag;

beforeEach(function () {
    $this->seoBuilder = new SeoBuilder;
    $this->page = new \Wotz\Seo\Tests\Fixtures\Models\Page;
});

it('can add a tag', function () {
    $tag = BaseTag::make($this->page, 'key');

    $this->seoBuilder->tag($tag);

    expect($this->seoBuilder)
        ->toHaveCount(1)
        ->first()->toBe($tag);
});

it('can add multiple tag', function () {
    $tagOne = [
        'type' => BaseTag::class,
        'name' => 'tag_one',
        'content' => 'content',
    ];

    $tagTwo = [
        'type' => BaseTag::class,
        'name' => 'tag_two',
        'content' => 'content',
    ];

    $this->seoBuilder->tags([$tagOne, $tagTwo]);

    expect($this->seoBuilder)
        ->toHaveCount(2)
        ->sequence(
            fn ($tag) => $tag
                ->toBeInstanceOf(BaseTag::class)
                ->getIdentifier()->toBe('tag_one'),
            fn ($tag) => $tag
                ->toBeInstanceOf(BaseTag::class)
                ->getIdentifier()->toBe('tag_two'),
        );
});

it('can add multiple tag and overwrite existing items', function () {
    $this->seoBuilder->tag(BaseTag::make($this->page, 'tag_one', 'content one old'));

    $tagOne = [
        'type' => BaseTag::class,
        'name' => 'tag_one',
        'content' => 'content one',
    ];

    $tagTwo = [
        'type' => BaseTag::class,
        'name' => 'tag_two',
        'content' => 'content two',
    ];

    $this->seoBuilder->tags([$tagOne, $tagTwo], true);

    expect($this->seoBuilder)
        ->toHaveCount(2)
        ->sequence(
            fn ($tag) => $tag
                ->toBeInstanceOf(BaseTag::class)
                ->getIdentifier()->toBe('tag_one')
                ->getContent()->toBe('content one'),
            fn ($tag) => $tag
                ->toBeInstanceOf(BaseTag::class)
                ->getIdentifier()->toBe('tag_two')
                ->getContent()->toBe('content two'),
        );
});

it('can add multiple tag and will not overwrite existing items', function () {
    $tag = BaseTag::make($this->page, 'tag_one')
        ->content('content one old');

    $this->seoBuilder->tag($tag);

    $tagOne = [
        'type' => BaseTag::class,
        'name' => 'tag_one',
        'content' => 'content one',
    ];

    $tagTwo = [
        'type' => BaseTag::class,
        'name' => 'tag_two',
        'content' => 'content two',
    ];

    $this->seoBuilder->tags([$tagOne, $tagTwo], false);

    expect($this->seoBuilder)
        ->toHaveCount(2)
        ->sequence(
            fn ($tag) => $tag
                ->toBeInstanceOf(BaseTag::class)
                ->getIdentifier()->toBe('tag_one')
                ->getContent()->toBe('content one old'),
            fn ($tag) => $tag
                ->toBeInstanceOf(BaseTag::class)
                ->getIdentifier()->toBe('tag_two')
                ->getContent()->toBe('content two'),
        );
});

it('can render', function () {
    config([
        'filament-seo.default' => [],
    ]);

    $tag = BaseTag::make($this->page, 'key')
        ->content('content');

    $this->seoBuilder->tag($tag);

    expect($this->seoBuilder)
        ->render()->toBe('<meta name="key" content="content">');
});

it('can render with defaults', function () {
    $defaultTag = [
        'type' => BaseTag::class,
        'name' => 'default',
        'content' => 'default content',
    ];

    config([
        'filament-seo.default' => [$defaultTag],
    ]);

    $tag = BaseTag::make($this->page, 'key')
        ->content('content');

    $this->seoBuilder->tag($tag);

    expect($this->seoBuilder)
        ->render()->toBe('<meta name="key" content="content"><meta name="default" content="default content">');
});

it('can get contents', function () {
    $tag = BaseTag::make($this->page, 'key')
        ->content('content');

    $this->seoBuilder->tag($tag);

    expect($this->seoBuilder->contents())
        ->toArray()->toBe(['key' => 'content']);
});

it('can get tag', function () {
    $tag = BaseTag::make($this->page, 'key')
        ->content('content');

    $this->seoBuilder->tag($tag);

    expect($this->seoBuilder)
        ->get('key')->getContent()->toBe('content');
});
