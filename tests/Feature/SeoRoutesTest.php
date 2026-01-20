<?php

use Illuminate\Support\Facades\Route;
use Wotz\MediaLibrary\Models\Attachment;
use Wotz\Seo\Facades\SeoBuilder;
use Wotz\Seo\Http\Middleware\SeoMiddleware;
use Wotz\Seo\Models\SeoRoute;
use Wotz\Seo\SeoRoutes;
use Wotz\Seo\Tests\Fixtures\Models\Page;

beforeEach(function () {
    $this->seoRoutes = new SeoRoutes;
});

it('can list the seo routes when no routes are given', function () {
    expect(SeoRoutes::list())
        ->toHaveCount(0);
});

it('can list the seo routes for 1 seo route', function () {
    Route::get('test', fn () => 'test')
        ->name('test')
        ->middleware(SeoMiddleware::class);

    expect(SeoRoutes::list())
        ->toHaveCount(1)
        ->sequence(
            fn ($route) => $route
                ->as->toBe('test')
                ->methods->toBe(['GET', 'HEAD'])
                ->action->toBeInstanceOf(Closure::class)
                ->middleware->toBe([SeoMiddleware::class])
        );
});

it('can list the seo routes and excludes routes without middleware', function () {
    Route::get('test', fn () => 'test')
        ->name('test')
        ->middleware(SeoMiddleware::class);

    Route::get('no-middleware', fn () => 'no-middleware')
        ->name('no-middleware');

    expect(SeoRoutes::list())
        ->toHaveCount(1);
});

it('can list the seo routes and excludes routes without name', function () {
    Route::get('test', fn () => 'test')
        ->name('test')
        ->middleware(SeoMiddleware::class);

    Route::get('no-middleware', fn () => 'no-middleware')
        ->middleware(SeoMiddleware::class);

    expect(SeoRoutes::list())
        ->toHaveCount(1);
});

it('can build for a given route and no entity', function () {
    $attachment = Attachment::withoutEvents(function () {
        return Attachment::factory()->create();
    });

    $seoRoute = SeoRoute::create([
        'route' => 'route.name',
        'og_type' => 'website',
        'og_image' => $attachment->id,
        'online' => true,
        'og_title' => 'og title',
        'og_description' => 'og description',
        'meta_title' => 'meta title',
        'meta_description' => 'meta description',
    ]);

    SeoRoutes::build($seoRoute, null);

    expect(SeoBuilder::contents())
        ->toArray()->toBe([
            'og_type' => 'website',
            'og_image' => $attachment->getFormatOrOriginal('og-image'),
            'og_title' => 'og title',
            'og_description' => 'og description',
            'meta_title' => 'meta title',
            'meta_description' => 'meta description',
        ]);
});

it('can build for a given route and an entity', function () {
    $attachment = Attachment::withoutEvents(function () {
        return Attachment::factory()->create();
    });

    $seoRoute = SeoRoute::create([
        'route' => 'route.name',
        'og_type' => 'website',
        'og_image' => $attachment->id,
        'online' => true,
        'og_title' => 'og {{ title }}',
        'og_description' => 'og {{ description }}',
        'meta_title' => 'meta {{ title }}',
        'meta_description' => 'meta {{ description }}',
    ]);

    $page = Page::create([
        'title' => 'Page title',
        'description' => 'Page description',
    ]);

    SeoRoutes::build($seoRoute, $page);

    expect(SeoBuilder::contents())
        ->toArray()->toBe([
            'og_type' => 'website',
            'og_image' => $attachment->getFormatOrOriginal('og-image'),
            'og_title' => 'og Page title',
            'og_description' => 'og Page description',
            'meta_title' => 'meta Page title',
            'meta_description' => 'meta Page description',
        ]);
});
