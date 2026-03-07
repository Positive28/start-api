<?php

namespace App\Models;

use App\Models\Concerns\HasAdDetail;
use Illuminate\Database\Eloquent\Model;

class Poultry extends Model
{
    use HasAdDetail;

    protected $primaryKey = 'id';

    public $incrementing = true;

    protected $fillable = [
        'ad_id',
        'age_weeks',
        'eggs_per_month',
        'vaccinated',
    ];

    protected function casts(): array
    {
        return [
            'vaccinated' => 'boolean',
        ];
    }
}
