<?php

namespace Wotz\Seo\Tests\Fixtures\Models;

use Wotz\Seo\Models\Traits\HasSeoFields;
use Wotz\Seo\SeoTags;
use Wotz\Seo\Tags\BaseTag;
use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    use HasSeoFields;

    protected $fillable = [
        'title',
        'description',
    ];

    public function getSeoTags(): SeoTags
    {
        return SeoTags::make()
            ->add(BaseTag::make($this, 'title', 'title'))
            ->add(BaseTag::make($this, 'description', fn () => $this->body));
    }
}
