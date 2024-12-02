<?php

namespace App\Filament\App\Resources\OwnerResource\Pages;

use App\Enum\RoleEnum;
use App\Filament\App\Resources\OwnerResource;
use App\Models\Company;
use Filament\Actions;
use Filament\Facades\Filament;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\DB;

class CreateOwner extends CreateRecord
{
    protected static string $resource = OwnerResource::class;

    public function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user']['password'] = $data['user']['cpf_cnpj'];

        return $data;
    }

    public function handleRecordCreation(array $data): \Illuminate\Database\Eloquent\Model
    {
        /** @var Company $company */
        $company = Filament::getTenant();
        $owner = $company->owners()->make($data);

        DB::transaction(function () use ($company, $data, $owner) {
            $user = $company->users()->create($data['user'], ['role' => RoleEnum::OWNER->value]);
            $owner->user_id = $user->id;
            $owner->save();
        });

        return $owner;
    }
}
