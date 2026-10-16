<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CollectionCondition extends Model
{
    protected $fillable = [
        'collection_id',
        'condition_field',
        'condition_operator',
        'condition_value',
    ];

    public function collection(): BelongsTo
    {
        return $this->belongsTo(Collection::class);
    }
}
