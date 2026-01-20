<?php

namespace Wotz\Seo\Filament\Resources\Pages;

use Filament\Resources\Pages\EditRecord;
use Wotz\Seo\Filament\Resources\SeoRouteResource;
use Wotz\TranslatableTabs\Resources\Traits\HasTranslations;

class EditSeoRoute extends EditRecord
{
    use HasTranslations;

    protected static string $resource = SeoRouteResource::class;
}
