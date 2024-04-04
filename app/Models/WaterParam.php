<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WaterParam extends Model
{
    protected $table = 'water_params';

    use HasFactory;
    protected $fillable = [
        'channel_id',
        // Add 'channel_id' to the fillable array
        'water_parameter',
        'chart_id',
        'chart_title',
        'show_graph',
        'field_id',
        'min_level',
        // Add 'channel_id' to the fillable array
        'max_level',
        'min_safe',
        'max_safe',
        'normal_color',
        'warning_color',
        'unit',
        'line_graph_webview_link',
        'gauge_webview_link',

    ];
    public function channel():BelongsTo
    {
        return $this->belongsTo(Channel::class, 'channel_id', 'id');
    }
}