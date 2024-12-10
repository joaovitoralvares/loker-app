<?php

namespace App\Filament\App\Resources\ContractResource\Pages;

use App\Filament\App\Resources\ContractResource;
use App\Models\Contract;
use Filament\Actions;
use Filament\Facades\Filament;
use Filament\Resources\Pages\CreateRecord;

class CreateContract extends CreateRecord
{
    protected static string $resource = ContractResource::class;

    /**
     * @param array{
     *     owner_id: int,
     *     customer_id: int,
     *     vehicle_id: int,
     *     start_date: string,
     *     auto_renew: bool,
     *     security_deposit_amount: int,
     * } $data
     * @return Contract
     */
    public function handleRecordCreation(array $data): Contract
    {
        $action = app(\App\Actions\CreateContract::class);
        $data['company_id'] = Filament::getTenant()->id;
        return $action->execute($data);
    }
}
