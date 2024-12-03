<?php

namespace App\Models;

use Filament\Facades\Filament;
use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasTenants;
use Filament\Panel;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

/**
 * @property-read int $id
 * @property string $name
 * @property string $email
 * @property-write string $password
 * @property bool $is_admin
 * @property string $person_type
 * @property string $cpf_cnpj
 */
class User extends Authenticatable implements FilamentUser, HasTenants
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'is_admin',
        'person_type',
        'cpf_cnpj',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * @return BelongsToMany<Company, $this>
     */
    public function company()
    {
        /** @var Company $company */
        $company = Filament::getTenant();
        return $this->companies()->where('companies.id', $company->id);
    }

    /**
     * @return BelongsToMany<Company, $this>
     */
    public function companies(): BelongsToMany
    {
        return $this->belongsToMany(Company::class)
            ->withPivot('role');
    }

    /**
     * @return HasMany<CompanyUser, $this>
     */
    public function userCompanies(): HasMany
    {
        return $this->hasMany(CompanyUser::class);
    }

    /**
     * @param Panel $panel
     * @return bool
     * @throws \Exception
     */
    public function canAccessPanel(Panel $panel): bool
    {
        if ($panel->getId() === 'admin') {
            return $this->is_admin;
        }

        if ($panel->getId() === 'app') {
            return $this->companies()->exists();
        }

        return false;
    }

    /**
     * @param Panel $panel
     * @return Collection<int, Company>
     */
    public function getTenants(Panel $panel): Collection
    {
        return $this->companies;
    }

    /**
     * @param Model $tenant
     * @return bool
     */
    public function canAccessTenant(Model $tenant): bool
    {
        return true;
    }

    /**
     * @return Attribute<string, string>
     */
    public function cpfCnpj(): Attribute
    {
        return Attribute::make(
            set: fn ($value) => preg_replace('/\D/', '', $value)
        );
    }

    /**
     * @return Attribute<string, string>
     */
    public function name(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => Str::upper($value),
            set: fn ($value) => Str::upper($value),
        );
    }
}
