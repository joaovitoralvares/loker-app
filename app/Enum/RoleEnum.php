<?php

namespace App\Enum;

enum RoleEnum: string
{
    case MANAGER = 'manager';
    case OWNER = 'owner';
    case CUSTOMER = 'customer';

    public static function toOptions(): array
    {
        return [
            self::MANAGER->value => self::MANAGER->label(),
            self::OWNER->value => self::OWNER->label(),
            self::CUSTOMER->value => self::CUSTOMER->label(),
        ];
    }

    public function label(): string
    {
        return match($this) {
            self::MANAGER => 'Gestor',
            self::OWNER => 'Proprietário',
            self::CUSTOMER => 'Cliente',
        };
    }
}
