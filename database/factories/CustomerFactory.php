<?php

namespace Database\Factories;

use App\Enum\MaritalStatusEnum;
use App\Enum\RoleEnum;
use App\Models\Company;
use App\Models\CompanyUser;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Customer>
 */
class CustomerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'company_id' => Company::factory(),
            'marital_status' => fake()->randomElement(MaritalStatusEnum::cases()),
            'birthday' => fake()->date(),
            'gender' => fake()->randomElement(['male', 'female']),
            'profession' => fake()->jobTitle(),
            'cnh_number' => fake()->rg(false),
            'cnh_security_code' => (string) fake()->randomNumber(3),
            'cnh_category' => 'B',
            'cnh_expiration_date' => now()->addYear(),
        ];
    }

    public function configure(): static
    {
        return $this->afterCreating(function (Customer $customer) {
            CompanyUser::create([
                'user_id' => $customer->user_id,
                'company_id' => $customer->company_id,
                'role' => RoleEnum::CUSTOMER
            ]);
        });
    }
}
