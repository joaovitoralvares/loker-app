<?php

namespace App\Models;

use Carbon\Carbon;
use Database\Factories\CustomerFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

/**
 * @property-read int $id
 * @property int $user_id
 * @property int $company_id
 * @property string $marital_status
 * @property Carbon $birthday
 * @property string $gender
 * @property string $profession
 * @property string $cnh_number
 * @property string $cnh_security_code
 * @property string $cnh_category
 * @property Carbon $cnh_expiration_date
 */
class Customer extends Model
{
    /** @use HasFactory<CustomerFactory> */
    use HasFactory;

    protected $table = 'customers';

    protected $fillable = [
        'user_id',
        'company_id',
        'marital_status',
        'birthday',
        'gender',
        'profession',
        'cnh_number',
        'cnh_security_code',
        'cnh_category',
        'cnh_expiration_date',
    ];

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return BelongsTo<Company, $this>
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * @return Attribute<string, string>
     */
    public function profession(): Attribute
    {
        return Attribute::make(
            get: fn($value) => Str::upper($value),
            set: fn($value) => Str::upper($value),
        );
    }

    protected $casts = [
        'birthday' => 'date',
        'cnh_expiration_date' => 'date',
    ];
}
