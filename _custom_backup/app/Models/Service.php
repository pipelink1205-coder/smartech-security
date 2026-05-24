<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    protected $fillable = [
        'name', 'slug', 'description', 'icon',
        'price_from', 'is_active', 'order', 'color',
    ];

    protected $casts = ['is_active' => 'boolean'];

    public function scopeActive($q)   { return $q->where('is_active', true); }
    public function scopeOrdered($q)  { return $q->orderBy('order'); }
}
