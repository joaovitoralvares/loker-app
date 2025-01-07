<?php

namespace App\Filament\App\Resources\TransferResource\Pages;

use App\Filament\App\Resources\TransferResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTransfers extends ListRecords
{
    protected static string $resource = TransferResource::class;

    protected static ?string $title = 'Valores disponíveis para repasse';

    protected function getHeaderActions(): array
    {
        return [
        ];
    }
}
