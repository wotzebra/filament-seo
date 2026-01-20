<?php

namespace Wotz\Seo;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Wotz\Seo\Facades\SeoBuilder;
use Wotz\Seo\Http\Middleware\SeoMiddleware;
use Wotz\Seo\Models\SeoRoute;
use Wotz\Seo\Tags\Meta;
use Wotz\Seo\Tags\OpenGraph;
use Wotz\Seo\Tags\OpenGraphImage;

class SeoRoutes
{
    public static function list(): Collection
    {
        $routeCollection = Route::getRoutes();

        return collect($routeCollection)
            ->filter(function ($route) {
                if (empty($route->action['as']) ||
                    empty($route->action['uses']) ||
                    empty($route->action['middleware'])
                ) {
                    return false;
                }

                $middleware = $route->action['middleware'];

                return in_array('seo', $middleware)
                    || in_array(SeoMiddleware::class, $middleware);
            })
            ->map(function ($route) {
                $routeName = $route->action['as'] ?? '';

                if (isset($route->wheres['translatable_prefix'])) {
                    $routeName = Str::after($routeName, $route->wheres['translatable_prefix'] . '.');
                }

                $routeMiddleware = $route->action['middleware'] ?? [];

                return [
                    'as' => $routeName,
                    'methods' => $route->methods,
                    'action' => $route->action['uses'] ?? '',
                    'middleware' => $routeMiddleware,
                ];
            })
            ->unique('as')
            ->values();
    }

    public static function build(SeoRoute $seoRoute, ?Model $entity)
    {
        SeoBuilder::tags([
            [
                'type' => OpenGraph::class,
                'name' => 'type',
                'content' => $seoRoute->og_type,
            ],
            [
                'type' => OpenGraphImage::class,
                'name' => 'image',
                'content' => $seoRoute->og_image,
            ],
            [
                'type' => OpenGraph::class,
                'name' => 'title',
                'content' => self::fillPlaceholders($seoRoute->og_title, $entity),
            ],
            [
                'type' => OpenGraph::class,
                'name' => 'description',
                'content' => self::fillPlaceholders($seoRoute->og_description, $entity),
            ],
            [
                'type' => Meta::class,
                'name' => 'title',
                'content' => self::fillPlaceholders($seoRoute->meta_title, $entity),
            ],
            [
                'type' => Meta::class,
                'name' => 'description',
                'content' => self::fillPlaceholders($seoRoute->meta_description, $entity),
            ],
        ]);
    }

    public static function fillPlaceholders(?string $text, ?Model $entity)
    {
        if (! $entity) {
            return $text;
        }

        $text = preg_replace_callback('/{{ (?<keyword>.*?) }}/', function ($match) use ($entity) {
            $found = data_get($entity, $match['keyword']);
            if ($found) {
                return strip_tags($found);
            }
        }, $text);

        return $text;
    }
}
