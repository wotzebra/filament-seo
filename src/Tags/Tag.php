<?php

namespace Wotz\Seo\Tags;

interface Tag
{
    /**
     * Render the tag as html
     */
    public function render(): string;

    /**
     * Identifier of the tag
     */
    public function getIdentifier(): string;
}
