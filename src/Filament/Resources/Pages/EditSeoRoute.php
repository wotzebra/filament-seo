<?php

namespace Wotz\Seo\Filament\Resources\Pages;

use Wotz\Seo\Filament\Resources\SeoRouteResource;
use Wotz\TranslatableTabs\Resources\Traits\HasTranslations;
use Filament\Resources\Pages\EditRecord;

class EditSeoRoute extends EditRecord
{
    use HasTranslations;

    protected static string $resource = SeoRouteResource::class;
}
