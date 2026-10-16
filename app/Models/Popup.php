<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Popup extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'config',
        'is_active',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'config' => 'array',
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function events(): HasMany
    {
        return $this->hasMany(PopupEvent::class);
    }

    public function getConfig(): array
    {
        $config = is_array($this->config) ? $this->config : [];

        return $config;
    }
}
