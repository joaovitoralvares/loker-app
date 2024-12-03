<?php

use App\Enum\PersonTypeEnum;
use App\Enum\RoleEnum;
use App\Filament\App\Resources\CustomerResource;
use App\Filament\App\Resources\CustomerResource\Pages\CreateCustomer;
use App\Filament\App\Resources\CustomerResource\Pages\ListCustomers;
use App\Models\Company;
use App\Models\Customer;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Support\Str;
use function Pest\Livewire\livewire;

beforeEach(function () {
    $this->user = User::factory()
        ->create()
        ->fresh();
    $company = Company::factory()->create()->fresh();
    $this->user->companies()->attach($company, ['role' => RoleEnum::MANAGER]);
    $this->actingAs($this->user);
    Filament::setTenant($company);
});

it('can render customers page', function () {
    $this->get(CustomerResource::getUrl())->assertSuccessful();
});

it('can list customers', function () {
    $customers = Customer::factory()
        ->for(Filament::getTenant())
        ->count(10)
        ->create();

    livewire(ListCustomers::class)->assertCanSeeTableRecords($customers);
});

it('cannot list customers from another tenant', function () {
    $company = Company::factory()
        ->create()
        ->fresh();
    $customers = Customer::factory()
        ->for($company)
        ->count(10)
        ->create();

    livewire(ListCustomers::class)->assertCanNotSeeTableRecords($customers);

});

it('can create a new customer', function () {
    $data = [
        'user' => [
            'name' => fake()->name(),
            'email' => fake()->email(),
            'person_type' => PersonTypeEnum::INDIVIDUAL->value,
            'cpf_cnpj' => fake()->cpf(),
        ],
        'cnh_number' => fake()->rg(false),
        'cnh_expiration_date' => now()->addDays(30)->format('Y-m-d'),
        'cnh_security_code' => (string)fake()->randomNumber(3),
        'cnh_category' => 'B',
        'birthday' => now()->subYears(25)->format('Y-m-d'),
        'marital_status' => fake()->randomElement(\App\Enum\MaritalStatusEnum::cases())
    ];

    livewire(CreateCustomer::class)
        ->fillForm($data)
        ->call('create')
        ->assertHasNoErrors();

    $customer = Customer::with('user', 'company')->first();
    expect(Customer::count())->toBe(1)
        ->and($customer)->toBeInstanceOf(Customer::class)
        ->and($customer->user)->toBeInstanceOf(User::class)
        ->and($customer->user->name)->toBe(Str::upper($data['user']['name']))
        ->and($customer->user->email)->toBe($data['user']['email'])
        ->and($customer->user->person_type)->toBe($data['user']['person_type'])
        ->and($customer->cnh_number)->toBe($data['cnh_number'])
        ->and($customer->cnh_expiration_date->format('Y-m-d'))->toBe($data['cnh_expiration_date'])
        ->and($customer->cnh_security_code)->toBe($data['cnh_security_code'])
        ->and($customer->cnh_category)->toBe($data['cnh_category'])
        ->and($customer->birthday->format('Y-m-d'))->toBe($data['birthday'])
        ->and($customer->marital_status)->toBe($data['marital_status']->value)
        ->and($customer->user->company()->first()->id)->toBe($this->user->company()->first()->id);
});
