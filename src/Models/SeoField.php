<?php

namespace Wotz\Seo\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Spatie\Translatable\HasTranslations;
use Wotz\Seo\Models\Casts\StringOrArrayCast;

/**
 * @property string $model_type
 * @property string $model_id
 * @property string $locale
 * @property string $type
 * @property string $name
 * @property string $content
 * @property string $is_translatable
 */
class SeoField extends Model
{
    use HasTranslations;

    public $fillable = [
        'model_type',
        'model_id',
        'locale',
        'type',
        'name',
        'content',
        'is_translatable',
    ];

    public $translatable = [
        'content',
    ];

    protected $casts = [
        'content' => StringOrArrayCast::class,
    ];

    public function model(): MorphTo
    {
        return $this->morphTo();
    }

    public function isTranslatableAttribute(string $key): bool
    {
        if (! in_array($key, $this->translatable)) {
            return false;
        }

        return $this->is_translatable ?: false;
    }

    public function getCasts(): array
    {
        return array_merge(
            parent::getCasts(),
        );
    }
}
