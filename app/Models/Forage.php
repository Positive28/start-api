<?php

namespace App\Models;

use App\Models\Concerns\HasAdDetail;
use Illuminate\Database\Eloquent\Model;

class Forage extends Model
{
    use HasAdDetail;

    protected $primaryKey = 'id';

    public $incrementing = true;

    protected $fillable = [
        'ad_id',
        'moisture_level',
        'bale_type',
    ];
}
