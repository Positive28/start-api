<?php

namespace App\Models;

use App\Models\Concerns\HasAdDetail;
use Illuminate\Database\Eloquent\Model;

class Vegetable extends Model
{
    use HasAdDetail;

    protected $primaryKey = 'id';

    public $incrementing = true;

    protected $fillable = [
        'ad_id',
        'grade',
        'packaging',
        'harvest_date',
    ];

    protected function casts(): array
    {
        return [
            'harvest_date' => 'date',
        ];
    }
}
