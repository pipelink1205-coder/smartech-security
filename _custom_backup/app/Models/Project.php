<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    protected $fillable = [
        'title', 'category', 'description',
        'location', 'image', 'is_featured', 'year',
    ];

    protected $casts = ['is_featured' => 'boolean'];

    public function scopeFeatured($q) { return $q->where('is_featured', true); }

    public function getCategoryColorAttribute(): string
    {
        return match($this->category) {
            'Seguridad'   => '#1e3a5f',
            'Solar'       => '#1a3a1e',
            'IPTV'        => '#3a1e1e',
            'Domótica'    => '#2d1e3a',
            'Cerraduras'  => '#1e3a38',
            default       => '#334155',
        };
    }
}
