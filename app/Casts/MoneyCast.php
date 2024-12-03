<?php

namespace App\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;

/**
 * @implements CastsAttributes<float, string>
 */
class MoneyCast implements CastsAttributes
{
    /**
     * @param $model
     * @param string $key
     * @param string $value
     * @param array<string, mixed> $attributes
     * @return float
     */
    public function get($model, string $key, $value, array $attributes): float
    {
        return round(floatval($value) / 100, precision: 2);
    }

    /**
     * @param $model
     * @param string $key
     * @param string $value
     * @param array<string, mixed> $attributes
     * @return float
     */
    public function set($model, string $key, $value, array $attributes): float
    {
        $value = str_replace('.', '', $value);
        $value = str_replace(',', '.', $value);

        return round(floatval($value) * 100);
    }
}
