<?php

namespace App\Models\Vehicle;

use App\Models\Company;
use App\Models\Owner;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Vehicle extends Model
{
    protected $table = 'vehicles';

    protected $fillable = [
        'owner_id',
        'company_id',
        'category_id',
        'brand_id',
        'model_id',
        'status',
        'year',
        'plate',
        'color',
        'chassi',
        'renavam',
        'transmission',
        'image_url',
        'odometer',
        'engine'
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(Owner::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    public function model(): BelongsTo
    {
        return $this->belongsTo(VehicleModel::class);
    }
}
