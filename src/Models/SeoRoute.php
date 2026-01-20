<?php

namespace Wotz\Seo\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

/**
 * @property string $route
 * @property string $og_type
 * @property string $og_image
 * @property string $og_title
 * @property string $og_description
 * @property string $meta_title
 * @property string $meta_description
 */
class SeoRoute extends Model
{
    use HasTranslations;

    protected $fillable = [
        'route',
        'og_type',
        'og_image',
        'og_title',
        'og_description',
        'meta_title',
        'meta_description',
        'online',
    ];

    protected $translatable = [
        'og_title',
        'og_description',
        'meta_title',
        'meta_description',
        'online',
    ];

    public function useFallbackLocale(): bool
    {
        return false;
    }
}
