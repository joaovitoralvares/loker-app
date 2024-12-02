<?php

namespace App\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;

class MoneyCast implements CastsAttributes
{
    public function get($model, string $key, $value, array $attributes): float
    {
        return round(floatval($value) / 100, precision: 2);
    }

    public function set($model, string $key, $value, array $attributes): float
    {
        $value = str_replace('.', '', $value);
        $value = str_replace(',', '.', $value);

        return round(floatval($value) * 100);
    }
}
