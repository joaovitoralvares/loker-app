<?php

namespace App\Filament\App\Resources\CustomerResource\Pages;

use App\Enum\RoleEnum;
use App\Filament\App\Resources\CustomerResource;
use App\Models\Company;
use App\Models\User;
use Filament\Actions;
use Filament\Facades\Filament;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\DB;

class CreateCustomer extends CreateRecord
{
    protected static string $resource = CustomerResource::class;

    public function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user']['password'] = $data['user']['cpf_cnpj'];

        return $data;
    }

    public function handleRecordCreation(array $data): \Illuminate\Database\Eloquent\Model
    {
        $userData = $data['user'];
        unset($data['user']);
        /** @var Company $company */
        $company = Filament::getTenant();
        $customer = $company->customers()->make($data);

        DB::transaction(function () use ($company, $userData, $customer) {
            $user = $company->users()->create($userData, ['role' => RoleEnum::CUSTOMER->value]);
            $customer->user_id = $user->id;
            $customer->save();
        });

        return $customer;
    }
}
