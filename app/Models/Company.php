<?php

namespace App\Models;

use App\Models\Vehicle\Brand;
use App\Models\Vehicle\Category;
use App\Models\Vehicle\Vehicle;
use App\Models\Vehicle\VehicleModel;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Company extends Model
{
    protected $table = 'companies';

    protected $fillable = [
        'name',
        'cnpj'
    ];

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class)
            ->withPivot('role');
    }

    public function companyUsers(): HasMany
    {
        return $this->hasMany(CompanyUser::class);
    }

    public function customers(): HasMany
    {
        return $this->hasMany(Customer::class);
    }

    public function owners(): HasMany
    {
        return $this->hasMany(Owner::class);
    }

    public function brands(): HasMany
    {
        return $this->hasMany(Brand::class);
    }

    public function vehicleModels(): HasMany
    {
        return $this->hasMany(VehicleModel::class);
    }

    public function categories(): HasMany
    {
        return $this->hasMany(Category::class);
    }

    public function vehicles(): HasMany
    {
        return $this->hasMany(Vehicle::class);
    }
    public function cnpj(): Attribute
    {
        return Attribute::make(
            set: fn ($value) => preg_replace('/\D/', '', $value)
        );
    }
}
