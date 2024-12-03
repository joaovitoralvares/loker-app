<?php

namespace App\Models\Vehicle;

use App\Models\Company;
use App\Models\Owner;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;


/**
 * @property-read int $id
 * @property int $owner_id
 * @property int $company_id
 * @property int $category_id
 * @property int $brand_id
 * @property int $model_id
 * @property string $status
 * @property int $year
 * @property string $plate
 * @property string $color
 * @property string $chassi
 * @property string $renavam
 * @property string $transmission
 * @property string $image_url
 * @property int $odometer
 * @property string $engine
 */
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

    /**
     * @return BelongsTo<Company, $this>
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * @return BelongsTo<Owner, $this>
     */
    public function owner(): BelongsTo
    {
        return $this->belongsTo(Owner::class);
    }

    /**
     * @return BelongsTo<Category, $this>
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * @return BelongsTo<Brand, $this>
     */
    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    /**
     * @return BelongsTo<VehicleModel, $this>
     */
    public function model(): BelongsTo
    {
        return $this->belongsTo(VehicleModel::class);
    }

    /**
     * @return Attribute<string, string>
     */
    public function color(): Attribute
    {
        return Attribute::make(
            set: fn ($value) => Str::upper($value),
        );
    }

    /**
     * @return Attribute<string, string>
     */
    public function plate(): Attribute
    {
        return Attribute::make(
            set: fn ($value) => Str::upper($value),
        );
    }
}
