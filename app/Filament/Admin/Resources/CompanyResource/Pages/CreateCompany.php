<?php

namespace App\Filament\Admin\Resources\CompanyResource\Pages;

use App\Enum\RoleEnum;
use App\Filament\Admin\Resources\CompanyResource;
use App\Models\Company;
use App\Models\User;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Wizard\Step;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class CreateCompany extends CreateRecord
{
    use CreateRecord\Concerns\HasWizard;

    protected static string $resource = CompanyResource::class;
    protected static ?string $title = 'Cadastrar Empresa';
    protected static bool $canCreateAnother = false;

    public function getSteps(): array
    {
        return [
            Step::make('Dados da empresa')
                ->description('Adicione os dados da empresa')
                ->schema([
                    TextInput::make('name')
                        ->required()
                        ->maxLength(255)
                        ->autocapitalize()
                        ->placeholder('Nome da empresa')
                        ->label('Nome'),
                    TextInput::make('cnpj')
                        ->required()
                        ->placeholder('Cnpj da empresa')
                        ->label('CNPJ')
                        ->mask('99.999.999/9999-99')
                        ->placeholder('00.000.000/0000-00')
                        ->rule('cnpj')
                ]),
            Step::make('Usuário')
                ->description('Adicione um usuário gestor para a empresa')
                ->schema([
                    TextInput::make('user.name')
                        ->label('Nome do usuário')
                        ->required()
                        ->maxLength(255),
                    TextInput::make('user.email')
                        ->label('E-mail')
                        ->required()
                        ->maxLength(255)
                        ->email()

                ])
        ];
    }

    protected function handleRecordCreation(array $data): Model
    {
        $company = new Company();
        $company->name = $data['name'];
        $company->cnpj = $data['cnpj'];

        $user = new User();
        $user->name = $data['user']['name'];
        $user->email = $data['user']['email'];
        $user->password = $company->cnpj;

        DB::transaction(function () use ($user, $company) {
            $company->save();
            $user->save();
            $company->users()->attach($user, ['role' => RoleEnum::MANAGER->value]);
        });

        return $company;
    }
}
