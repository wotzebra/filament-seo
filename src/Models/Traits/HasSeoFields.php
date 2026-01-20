<?php

namespace Wotz\Seo\Models\Traits;

use Illuminate\Database\Eloquent\Model;
use Wotz\Seo\Facades\SeoBuilder;
use Wotz\Seo\Models\SeoField;
use Wotz\Seo\SeoTags;
use Wotz\Seo\Tags\BaseTag;

trait HasSeoFields
{
    public static function bootHasSeoFields()
    {
        static::deleting(function (Model $entity) {
            $entity->seoFields()->get()->each->delete();
        });
    }

    abstract public function getSeoTags(): SeoTags;

    /**
     * Set the polymorphic relation.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function seoFields()
    {
        return $this->morphMany(SeoField::class, 'model');
    }

    /**
     * Get all seo fields.
     */
    public function withSeoFields()
    {
        SeoBuilder::tags(
            $this->seoFields
                ->map(fn (SeoField $seoField) => [
                    'type' => $seoField->type,
                    'name' => $seoField->name,
                    'content' => $seoField->content,
                ])
                ->toArray()
        );
    }

    public function fillSeoFieldState(?string $locale = null)
    {
        return $this->seoFields->mapWithKeys(function (SeoField $seoField) use ($locale) {
            $tag = $this->getSeoTags()->firstForTypeAndKey($seoField->type, $seoField->name);

            if (! $tag) {
                return [];
            }

            if ($tag->isTranslatable() !== (bool) $locale) {
                return [];
            }

            $tag->content($tag->isTranslatable() ? $seoField->getTranslation('content', $locale, false) : $seoField->content);

            return [
                $tag->getIdentifier() => $tag->getContent(true),
            ];
        })->toArray() ?: [];
    }

    public function saveSeoFieldState(array $state = [])
    {
        $this->getSeoTags()
            ->each(function (BaseTag $tag) use ($state) {
                $stateValue = $state[$tag->getIdentifier()] ?? '';

                if ($tag->isTranslatable() && is_array($stateValue)) {
                    $content = [];
                    foreach ($stateValue as $locale => $value) {
                        $content[$locale] = $tag->beforeSave($value ?: $tag->getDefaultContent($locale));
                    }
                } else {
                    $content = $tag->beforeSave($stateValue ?: $tag->getDefaultContent());
                }

                $this->seoFields()->updateOrCreate(
                    [
                        'type' => $tag::class,
                        'name' => $tag->getKey(),
                    ],
                    [
                        'content' => $content,
                        'is_translatable' => $tag->isTranslatable(),
                    ]
                );
            });
    }
}
