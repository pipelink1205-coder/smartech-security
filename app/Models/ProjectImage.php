<?php

namespace App\Models;

use App\Support\ResolvesMediaPath;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProjectImage extends Model
{
    use ResolvesMediaPath;

    protected $fillable = ['project_id', 'path', 'caption', 'sort_order'];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function getUrlAttribute(): string
    {
        return $this->resolveMediaUrl($this->path) ?? asset('images/projects/placeholder.svg');
    }
}
