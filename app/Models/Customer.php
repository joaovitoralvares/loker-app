<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Customer extends Model
{
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

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }
}
