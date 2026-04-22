<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BackgroundSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'background_type',
        'background_image',
        'background_color',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function getBackgroundUrlAttribute()
    {
        if ($this->background_type === 'image' && $this->background_image) {
            return asset('storage/' . $this->background_image);
        }
        return null;
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order');
    }
}
