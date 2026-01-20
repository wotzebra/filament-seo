<?php

use Wotz\Seo\Tags\Meta;
use Wotz\Seo\Tags\OgUrl;
use Wotz\Seo\Tags\OpenGraph;
use Wotz\Seo\Tags\OpenGraphImage;

return [
    'models' => [
        'seo-route' => \Wotz\Seo\Models\SeoRoute::class,
    ],
    'default' => [
        'title_og' => [
            'type' => OpenGraph::class,
            'name' => 'title',
            'content' => config('app.name'),
        ],
        'title_meta' => [
            'type' => Meta::class,
            'name' => 'title',
            'content' => config('app.name'),
        ],
        'description_og' => [
            'type' => OpenGraph::class,
            'name' => 'description',
            'content' => '',
        ],
        'description_meta' => [
            'type' => Meta::class,
            'name' => 'description',
            'content' => '',
        ],
        'image_og' => [
            'type' => OpenGraphImage::class,
            'name' => 'image',
            'content' => '',
        ],
        'type_og' => [
            'type' => OpenGraph::class,
            'name' => 'type',
            'content' => 'website',
        ],
        'url_og' => [
            'type' => OgUrl::class,
            'name' => 'url',
            'content' => '',
        ],
    ],
];
