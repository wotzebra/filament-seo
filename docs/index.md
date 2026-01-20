# Package to manage SEO tags for models and routes in Filament

## Introduction

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

## Getting started

### Model

To add SEO tags to your model, you need to use the `HasSeoFields` trait and add a `getSeoTags` method.

```php
public function getSeoTags(): SeoTags
{
    return \Wotz\Seo\SeoTags::make()
        ->add(\Wotz\Seo\Tags\Meta::make($this, 'title', 'title')->translatable())
        ->add(
            \Wotz\Seo\Tags\OpenGraph::make(
                $this,
                'title',
                function (string $locale) {
                    return $this->working_title . ' ' . $locale;
                }
            )->translatable()
        )
        ->add(\Wotz\Seo\Tags\Meta::make($this, 'description', 'intro')->translatable())
        ->add(\Wotz\Seo\Tags\OpenGraph::make($this, 'description', fn (string $locale) => 'og description ' . $locale)->translatable())
        ->add(\Wotz\Seo\Tags\OpenGraphImage::make($this, 'image', fn () => $this->attachment_id));
}
```

To read more about how these tags work, check out the Tags section below.

### Filament Resource

To add the Seo tags from your model in your Filament form, you only have to use `SeoCard::make(static::getModel())`

```php
public static function form(Form $form): Form
{
    return $form->schema([
        // ...
        \Wotz\Seo\Filament\SeoCard::make(static::getModel()),
        // ...
    ]);
}
```

Since tags are translatable (we use [`spatie/laravel-translatable`](https://spatie.be/docs/laravel-translatable/v6/introduction) for this) you can also pass a locale to the SeoCard.

E.g. if you work with our [Translatable Tabs](https://github.com/wotzebra/filament-translatable-tabs) package:

```php
public static function form(Form $form): Form
{
    return $form->schema([
        TranslatableTabs::make('Translations')
            ->defaultFields([
                // ...

                SeoCard::make(static::getModel()),
            ])
            ->translatableFields(fn (string $locale) => [
                // ...
                
                SeoCard::make(static::getModel(), $locale),
                
            ]),
    ]);
}
```

Next to that you also have to add the `\Wotz\Seo\Filament\Traits\SavesSeoFields` trait to your Create and Edit Page for this resource.
We need this since we have to modify the state before saving it.

### Routes

To make your routes pop up in the Seo Routes module in Filament, you have to attach the `\Wotz\Seo\Http\Middleware\SeoMiddleware` middleware.

```php
Route::get('/', HomeController::class)
    ->name('home')
    ->middleware(\Wotz\Seo\Http\Middleware\SeoMiddleware::class);
```

Then run the `php artisan seo:import` command to add the routes to the database.
We recommend to run this command in your deployment script.

### Front-end

Add the `@seo()` directive in the `<head>` section of your layout.

```html
<!doctype html>
<html lang="en">
<head>
    <!--...-->
    @seo()
    <!--...-->
</head>
<!--...-->
</html>
```

The directive will render the SEO tags for the current route and model.

It will first look on the model, then the routes and then the default config to fill in the values.

## Seo Tags

Default we have support for the following tags:

- `Wotz\Seo\Tags\Meta`
- `Wotz\Seo\Tags\OpenGraph`
- `Wotz\Seo\Tags\OpenGraphImage`
- `Wotz\Seo\Tags\OgUrl`

These all extend the BaseTag class and implement the Tag interface.

So to add your own tag, just create a class that extends the BaseTag class and implements the Tag interface.

```php
<?php

namespace Wotz\Seo\Tags;

class CustomImplementation extends BaseTag
{
    protected string $identifierPrefix = 'custom_';
}
```

When making a tag, you have to pass the following parameters:

- Model $model: so we can set the content of the tag 
- string $key: the key of the tag, will mostly be title or description
- string|Closure|null $defaultAttribute = null: we use this to fill up the seo field for the model if it's not filled in
  - can be a string (model property, e.g. `title`)
  - closure, returns a string (e.g. `fn (string $locale) => 'og description ' . $locale`)
  - null: if it can be left empty
- array $settings = []: to pass the rules or own needs for your custom tag

Each tag can be translatable, can be defined by chaining the `->translatable()` method to your tag
