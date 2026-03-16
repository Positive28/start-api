<?php

namespace App\Models\Concerns;

trait HasAdDetail
{
    /**
     * Detail jadvalda PK — id (Laravel default). ad_id unique, lekin PK emas.
     */
    public function ad()
    {
        return $this->belongsTo(\App\Models\Ad::class);
    }
}
