<?php

use Wotz\Seo\Facades\SeoBuilder;
use Wotz\Seo\Models\SeoField;
use Wotz\Seo\Tags\BaseTag;
use Wotz\Seo\Tests\Fixtures\Models\Page;

beforeEach(function () {
    $this->page = Page::create([
        'title' => 'title',
        'description' => 'description',
    ]);

    $this->page->seoFields()->createMany([
        [
            'type' => BaseTag::class,
            'name' => 'title',
            'content' => 'seo title',
            'is_translatable' => false,
        ],
        [
            'type' => BaseTag::class,
            'name' => 'description',
            'content' => '',
            'is_translatable' => false,
        ],
    ]);
});

it('will delete the seo fields if main model is deleted', function () {
    $this->assertDatabaseCount(SeoField::class, 2);

    $this->page->delete();

    $this->assertDatabaseCount(SeoField::class, 0);
});

it('sets the seo builder tags', function () {
    SeoBuilder::shouldReceive('tags')
        ->once()
        ->with($this->page->seoFields
            ->map(fn (SeoField $seoField) => [
                'type' => $seoField->type,
                'name' => $seoField->name,
                'content' => $seoField->content,
            ])
            ->toArray()
        );

    $this->page->withSeoFields();
});

it('can save the seo field state', function () {
    $this->page->saveSeoFieldState([
        'title' => 'new title',
        'description' => 'new description',
    ]);

    $this->assertDatabaseHas(SeoField::class, [
        'type' => BaseTag::class,
        'name' => 'title',
        'content' => json_encode('new title'),
    ]);

    $this->assertDatabaseHas(SeoField::class, [
        'type' => BaseTag::class,
        'name' => 'description',
        'content' => json_encode('new description'),
    ]);
});

it('skips the seo fields that are not in the state', function () {
    $this->page->saveSeoFieldState([
        'title' => 'seo title',
        'description' => 'new description',
    ]);

    $this->assertDatabaseHas(SeoField::class, [
        'type' => BaseTag::class,
        'name' => 'title',
        'content' => json_encode('seo title'),
    ]);

    $this->assertDatabaseHas(SeoField::class, [
        'type' => BaseTag::class,
        'name' => 'description',
        'content' => json_encode('new description'),
    ]);
});

it('will use the default value if content is empty', function () {
    $this->page->saveSeoFieldState([
        'title' => '',
    ]);

    $this->assertDatabaseHas(SeoField::class, [
        'type' => BaseTag::class,
        'name' => 'title',
        'content' => json_encode('title'),
    ]);
});

it('can init the seo', function () {
    $page = Page::create([
        'title' => 'Title',
        'description' => json_encode('Description'),
    ]);

    // 2 from beforeEach
    $this->assertDatabaseCount(SeoField::class, 2);

    $page->saveSeoFieldState();

    $this->assertDatabaseCount(SeoField::class, 4);

    $this->assertDatabaseHas(SeoField::class, [
        'model_type' => Page::class,
        'model_id' => $page->id,
        'type' => BaseTag::class,
        'name' => 'title',
        'content' => json_encode('Title'),
    ]);

    $this->assertDatabaseHas(SeoField::class, [
        'model_type' => Page::class,
        'model_id' => $page->id,
        'type' => BaseTag::class,
        'name' => 'description',
        'content' => json_encode(''),
    ]);
});
