<?php

namespace App\Models;

use App\Models\Concerns\HasAdDetail;
use Illuminate\Database\Eloquent\Model;

class Animal extends Model
{
    use HasAdDetail;

    protected $primaryKey = 'id';

    public $incrementing = true;

    protected $fillable = [
        'ad_id',
        'age_months',
        'weight_kg',
        'breed',
        'purpose',
        'health',
        'milk_per_day_l',
        'is_pregnant',
    ];

    protected function casts(): array
    {
        return [
            'weight_kg'      => 'decimal:1',
            'milk_per_day_l' => 'decimal:1',
            'is_pregnant'    => 'boolean',
        ];
    }
}
