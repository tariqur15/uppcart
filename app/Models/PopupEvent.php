<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PopupEvent extends Model
{
    protected $fillable = [
        'popup_id',
        'type',
        'page',
        'device',
    ];

    public function popup(): BelongsTo
    {
        return $this->belongsTo(Popup::class);
    }
}
