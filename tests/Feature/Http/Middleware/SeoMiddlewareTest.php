<?php

use Wotz\Seo\Facades\SeoBuilder;
use Wotz\Seo\Http\Middleware\SeoMiddleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Symfony\Component\HttpFoundation\Request as HttpFoundationRequest;

it('will not the seo routes if no route is found', function () {
    Route::get('', fn () => 'route');

    SeoBuilder::shouldReceive('build')
        ->never();

    app(SeoMiddleware::class)->handle(Request::create(''), fn () => '');
});

it('will build the seo routes when route matches', function () {
    $route = Route::get('test', fn () => 'route')
        ->name('test');

    \Wotz\Seo\Models\SeoRoute::create([
        'route' => 'test',
        'og_type' => 'site',
        'description' => 'test',
        'online' => true,
    ]);

    SeoBuilder::shouldReceive('tags')
        ->once();

    $symfonyRequest = HttpFoundationRequest::create(
        'test', 'GET'
    );

    $request = Request::createFromBase($symfonyRequest);
    $request->setRouteResolver(fn () => $route);

    app(SeoMiddleware::class)->handle($request, fn () => '');
});
