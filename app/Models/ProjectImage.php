<?php

namespace App\Models;

use App\Support\ResolvesMediaPath;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProjectImage extends Model
{
    use ResolvesMediaPath;

    protected $fillable = ['project_id', 'path', 'caption', 'sort_order', 'is_cover'];

    protected $casts = [
        'is_cover' => 'boolean',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function getUrlAttribute(): string
    {
        return $this->resolveMediaUrl($this->path) ?? '/images/projects/placeholder.svg';
    }
}
