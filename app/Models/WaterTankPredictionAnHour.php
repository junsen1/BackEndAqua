<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WaterTankPredictionAnHour extends Model
{
    use HasFactory;
    protected $table = 'water_tank_prediction_an_hour';

    protected $fillable = [
        'channel_id',
        // Add 'channel_id' to the fillable array
        'water_parameter',
        'value'
    ];
}
