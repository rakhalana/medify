<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentMethod extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'type',
        'same_city_only',
        'is_active',
        'instructions',
    ];

    protected function casts(): array
    {
        return [
            'same_city_only' => 'boolean',
            'is_active' => 'boolean',
        ];
    }
}
