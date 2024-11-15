<?php

namespace App\Models;

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

    public function cnpj(): Attribute
    {
        return Attribute::make(
            set: fn ($value) => preg_replace('/\D/', '', $value)
        );
    }
}
