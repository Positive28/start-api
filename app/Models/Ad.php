<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ad extends Model
{
    protected $fillable = [
        'seller_id',
        'category_id',
        'subcategory_id',
        'title',
        'price',
        'quantity',
        'unit',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'price'    => 'decimal:2',
            'quantity' => 'decimal:2',
        ];
    }

    public function seller()
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function subcategory()
    {
        return $this->belongsTo(Subcategory::class);
    }

    public function animal()
    {
        return $this->hasOne(Animal::class);
    }

    public function poultry()
    {
        return $this->hasOne(Poultry::class);
    }

    public function grain()
    {
        return $this->hasOne(Grain::class);
    }

    public function fruit()
    {
        return $this->hasOne(Fruit::class);
    }

    public function forage()
    {
        return $this->hasOne(Forage::class);
    }

    public function vegetable()
    {
        return $this->hasOne(Vegetable::class);
    }
}
