<?php

namespace App\Enum;

enum VehicleStatusEnum: string
{
    case AVAILABLE = 'available';
    case RENTED = 'rented';
    case UNDER_MAINTENANCE = 'under_maintenance';
    case FOR_SALE = 'for_sale';

    public function label(): string
    {
        return match ($this) {
            self::AVAILABLE => 'Disponível',
            self::RENTED => 'Alugado',
            self::UNDER_MAINTENANCE => 'Em manutenção',
            self::FOR_SALE => 'À venda',
        };
    }

    public static function toOptions(): array
    {
        return [
            self::AVAILABLE->value => self::AVAILABLE->label(),
            self::RENTED->value => self::RENTED->label(),
            self::UNDER_MAINTENANCE->value => self::UNDER_MAINTENANCE->label(),
            self::FOR_SALE->value => self::FOR_SALE->label(),
        ];
    }

}
