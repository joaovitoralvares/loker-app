<?php

namespace App\Filament\App\Resources\TransferResource\Pages;

use App\Filament\App\Resources\TransferResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTransfers extends ListRecords
{
    protected static string $resource = TransferResource::class;

    protected static ?string $title = 'Gestão de repasses';

    protected function getHeaderActions(): array
    {
        return [
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            TransferResource\Widgets\TransactionsAvailableForTransfer::class
        ];
    }
}
