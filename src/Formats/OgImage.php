<?php

namespace Wotz\Seo\Formats;

use Wotz\MediaLibrary\Formats\Format;
use Wotz\MediaLibrary\Formats\Manipulations;
use Wotz\Seo\Models\SeoRoute;
use Spatie\Image\Drivers\ImageDriver;
use Spatie\Image\Enums\Fit;

class OgImage extends Format
{
    protected string $name = 'OG Image';

    protected string $description = 'Format used to display the image for SEO purposes';

    public function definition(): Manipulations|ImageDriver
    {
        return $this->manipulations->fit(Fit::Contain, 1200, 630);
    }

    public function registerModelsForFormatter(): void
    {
        $this->registerFor(SeoRoute::class);
    }
}
