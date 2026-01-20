<?php

namespace Wotz\Seo\Models\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Casts\Json;

class StringOrArrayCast implements CastsAttributes
{
    public function get($model, $key, $value, $attributes)
    {
        if (! isset($attributes[$key])) {
            return;
        }

        $data = Json::decode($attributes[$key]);

        return is_array($data) ? $data : $attributes[$key];
    }

    public function set($model, $key, $value, $attributes)
    {
        return [
            $key => is_array($value) ? Json::encode($value) : $value,
        ];
    }
}
