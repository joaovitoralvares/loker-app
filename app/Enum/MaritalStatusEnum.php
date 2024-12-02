<?php

namespace App\Enum;

enum MaritalStatusEnum: string
{
    case SINGLE = 'single';
    case MARRIED = 'married';
    case WIDOW = 'widow';
    case DIVORCED = 'divorced';

    public function label(): string
    {
        return match ($this) {
            self::SINGLE => 'Solteiro(a)',
            self::MARRIED => 'Casado(a)',
            self::WIDOW => 'Viúvo(a)',
            self::DIVORCED => 'Divorciado(a)',
        };
    }

    public static function toOptions(): array
    {
        return [
            self::SINGLE->value => self::SINGLE->label(),
            self::MARRIED->value => self::MARRIED->label(),
            self::WIDOW->value => self::WIDOW->label(),
            self::DIVORCED->value => self::DIVORCED->label(),
        ];
    }

}
