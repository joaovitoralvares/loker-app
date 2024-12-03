<?php

namespace App\Enum;

enum VehicleTransmissionEnum: string
{
    case AUTOMATIC = 'automatic';
    case MANUAL = 'manual';

    public function label(): string
    {
        return match ($this) {
            self::AUTOMATIC => 'Automático',
            self::MANUAL => 'Manual',
        };
    }

    /**
     * @return string[]
     */
    public static function toOptions(): array
    {
        return [
            self::AUTOMATIC->value => self::AUTOMATIC->label(),
            self::MANUAL->value => self::MANUAL->label(),
        ];
    }

}
