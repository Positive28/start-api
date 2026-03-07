<?php

namespace App\Models;

use App\Models\Concerns\HasAdDetail;
use Illuminate\Database\Eloquent\Model;

class Grain extends Model
{
    use HasAdDetail;

    protected $primaryKey = 'id';

    public $incrementing = true;

    protected $fillable = [
        'ad_id',
        'packaging',
    ];
}
