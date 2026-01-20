<?php

use Wotz\Seo\SeoTags;
use Wotz\Seo\Tags\BaseTag;
use Wotz\Seo\Tests\Fixtures\Models\Page;

beforeEach(function () {
    $this->seoTags = new SeoTags;
});

it('can make an instance', function () {
    expect(SeoTags::make())
        ->toBeInstanceOf(SeoTags::class);
});

it('can add a tag', function () {
    $this->seoTags->add(BaseTag::make(new Page, 'base_tag'));

    expect($this->seoTags)
        ->toHaveCount(1);
});

it('can return a tag when a type and key are given', function () {
    $tag1 = BaseTag::make(new Page, 'base_tag', 'default');
    $tag2 = BaseTag::make(new Page, 'base_tag_2', 'default_2');

    $this->seoTags->add($tag1);
    $this->seoTags->add($tag2);

    expect($this->seoTags)
        ->firstForTypeAndKey(BaseTag::class, 'base_tag_2')
        ->toBe($tag2);
});
