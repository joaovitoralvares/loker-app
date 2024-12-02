<?php

namespace App\Filament\App\Resources\VehicleModelResource\Pages;

use App\Filament\App\Resources\VehicleModelResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageVehicleModels extends ManageRecords
{
    protected static string $resource = VehicleModelResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
