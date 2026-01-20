# Package to manage SEO tags for models and routes in Filament

The SEO package for Laravel and Filament is a tool that simplifies the management of Open Graph (OG) and meta tags for 
your Eloquent models and routes. With this package, you can effortlessly define and customize essential metadata 
such as titles, descriptions and images, optimizing how your content appears in search results and 
on social media platforms. 
Integrated with Filament, the package offers a user-friendly interface for easy configuration, empowering you to 
enhance your website's SEO capabilities and drive more organic traffic to your site.

## Installation

You can install the package via composer:

```bash
composer require wotz/filament-seo
```

You can publish and run the migrations with:

```bash
php artisan vendor:publish --tag="filament-seo-migrations"
php artisan migrate
```

You can publish the config file with:

```bash
php artisan vendor:publish --tag="filament-seo-config"
```

This is the contents of the published config file:

```php
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
```

## Usage

```php
// Filament
\Wotz\Seo\Filament\SeoCard::make();

// Front-end
@seo()
```

## Documentation

For the full documentation, check [here](./docs/index.md).

## Testing

```bash
vendor/bin/pest
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Upgrading

Please see [UPGRADING](UPGRADING.md) for more information on how to upgrade to a new version.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security Vulnerabilities

If you discover any security-related issues, please email info@whoownsthezebra.be instead of using the issue tracker.

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
