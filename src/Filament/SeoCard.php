<?php

namespace Wotz\Seo\Filament;

use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Illuminate\Database\Eloquent\Model;
use Wotz\MediaLibrary\Filament\AttachmentInput;
use Wotz\Seo\Formats\OgImage;
use Wotz\Seo\Tags\BaseTag;
use Wotz\Seo\Tags\OpenGraphImage;

class SeoCard
{
    public static function make(string $model, ?string $locale = null): Section
    {
        $model = app($model);

        $fields = $model->getSeoTags()
            ->filter(fn (BaseTag $tag) => $tag->isTranslatable() === (bool) $locale)
            ->map(function (BaseTag $tag) {
                if ($tag::class === OpenGraphImage::class) {
                    return AttachmentInput::make($tag->getIdentifier())
                        ->rules($tag->getRules())
                        ->allowedFormats([
                            OgImage::class,
                        ]);
                }

                return Textarea::make($tag->getIdentifier())
                    ->rules($tag->getRules());
            });

        // A real Section heading, rather than a `label('Seo')` on the section *and* a
        // TextEntry faking a heading with `text-2xl font-bold` inside it — which rendered
        // the word "Seo" twice, in two different type styles, neither of them the
        // design system's section heading.
        return Section::make('SEO')
            ->columns(1)
            ->schema([
                Group::make([
                    ...$fields->toArray(),
                ])
                    ->afterStateHydrated(function (Group $component, ?Model $record) use ($locale): void {
                        $component->getChildSchema()->fill($record?->fillSeoFieldState($locale));
                    })
                    ->statePath('seoFields'),
            ]);
    }
}
