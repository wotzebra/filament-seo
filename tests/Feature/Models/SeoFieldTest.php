<?php

use Wotz\Seo\Models\SeoField;
use Illuminate\Database\Eloquent\Relations\MorphTo;

it('has fillable fields', function () {
    $seoField = new SeoField;

    expect($seoField)
        ->fillable->toBe([
            'model_type',
            'model_id',
            'locale',
            'type',
            'name',
            'content',
            'is_translatable',
        ]);
});

it('has a morph to relationship', function () {
    $seoField = new SeoField;

    expect($seoField)
        ->model()->toBeInstanceOf(MorphTo::class);
});
