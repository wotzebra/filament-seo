<?php

namespace Wotz\Seo;

use Illuminate\Support\Collection;
use Wotz\Seo\Models\SeoRoute;
use Wotz\Seo\Tags\Tag;

class SeoBuilder extends Collection
{
    /**
     * Add a tag to the items
     */
    public function tag(Tag $item): void
    {
        // Use key as unique identifier, so we can't add multiple tags with same name/property
        $this->put($item->getIdentifier(), $item);
    }

    /** Convert arrays with key: type, name, content to a tag class */
    public function tags(array $items, bool $overwrite = true)
    {
        collect($items)
            ->map(fn (array $item) => $item['type']::make(
                new SeoRoute,
                $item['name']
            )->content($item['content']))
            ->each(function (Tag $tag) use ($overwrite) {
                $exists = $this->has($tag->getIdentifier());
                if (($exists && $overwrite) || ! $exists) {
                    $this->tag($tag);
                }
            });
    }

    /**
     * Render the tags
     */
    public function render(): string
    {
        $this->setDefaults();

        return $this->map->render()
            ->implode('');
    }

    public function contents(): Collection
    {
        return $this->map->getContent();
    }

    /**
     * Set the default tags from the seo config
     *
     * @return void
     */
    protected function setDefaults()
    {
        $this->tags(config('filament-seo.default', []), false);
    }
}
