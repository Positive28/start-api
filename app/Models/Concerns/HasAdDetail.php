<?php

namespace App\Models\Concerns;

trait HasAdDetail
{
    protected $primaryKey = 'ad_id';

    public $incrementing = false;

    public function ad()
    {
        return $this->belongsTo(\App\Models\Ad::class);
    }
}
