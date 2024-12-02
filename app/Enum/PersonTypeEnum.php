<?php

namespace App\Enum;

enum PersonTypeEnum: string
{
    case INDIVIDUAL = 'individual';
    case CORPORATE = 'corporate';

    public function label(): string
    {
        return match ($this) {
            self::INDIVIDUAL => 'Pessoa Física',
            self::CORPORATE => 'Pessoa Jurídica',
        };
    }

    public static function toOptions(): array
    {
        return [
            self::INDIVIDUAL->value => self::INDIVIDUAL->label(),
            self::CORPORATE->value => self::CORPORATE->label(),
        ];
    }

}
