<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Action extends Model
{
    use HasFactory;

    protected $fillable = [
        'channel_id',
        'title',
        'description',
        'date',
        'start_time',
        'end_time',
        'color',
    ];

    public function channel(): BelongsTo
    {
        return $this->belongsTo(Channel::class, 'channel_id');
    }
}
