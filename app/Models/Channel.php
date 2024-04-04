<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Channel extends Model
{
    protected $table = 'channels';
    use HasFactory;
    protected $fillable = ['user_id', 'channel_id'];
    
    public function waterparams(): HasMany
    {
        return $this->hasMany(WaterParam::class, 'channel_id', 'id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function actions(): HasMany
    {
        return $this->hasMany(Action::class, 'channel_id');
    }
}
