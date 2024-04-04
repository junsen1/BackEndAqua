<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WaterTankPredictionMonthDay extends Model
{
    protected $table = 'water_tank_prediction_month_day';

    use HasFactory;
    protected $fillable = [
        'channel_id',
        // Add 'channel_id' to the fillable array
        'water_parameter',
        'value'
    ];
}
