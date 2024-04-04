<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\ChannelsController;
use App\Http\Controllers\WaterParamsController;
use App\Http\Controllers\ActionController;
use App\Http\Controllers\PredictionController;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Route::get('register', [RegisteredUserController::class, 'create'])->name('register.api');

// Routes protected by auth:api middleware
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/channels', [ChannelsController::class, 'store']);
    Route::get('/channels', [ChannelsController::class, 'getAllChannels']);
    Route::get('/channels/{channelId}', [ChannelsController::class, 'getChannelById']);
    Route::delete('/channels/{channelId}', [ChannelsController::class, 'deleteChannelById']);
    
    Route::post('/waterparams', [WaterParamsController::class, 'byUserId']);
    Route::put('/waterparams', [WaterParamsController::class, 'update']);
    Route::get('/waterparams/{channelId}', [WaterParamsController::class, 'getWaterParamById']);


    Route::get('/actions', [ActionController::class, 'index']);
    Route::post('/actions', [ActionController::class, 'store']);
    Route::get('/actions/channel/{channel_id}', [ActionController::class, 'showByChannel']);
    Route::put('/actions/{action}', [ActionController::class, 'update']);
    Route::delete('/actions/{action}', [ActionController::class, 'destroy']);

    Route::post('actions/interval/{channel_id}', [ActionController::class, 'getActivityIntervals']);
    
    Route::post('actions/lastinterval/{channel_id}',  [ActionController::class, 'getLastActivityIntervals']);
    Route::post('actions/duration/{channel_id}', [ActionController::class, 'calculateDurationForCurrentMonth']);
    Route::post('actions/frequency/{channel_id}',  [ActionController::class, 'getCurrentAndLastMonthFrequency']);
    Route::post('actions/averageinterval/{channel_id}',  [ActionController::class, 'getAverageIntervals']);
    
    Route::post('/predict', [PredictionController::class, 'predict']);

});
Route::post('/createPredictionDayHour', [PredictionController::class, 'updateOrCreatePredictionDayHour']);
Route::post('/createPredictionWeekDay', [PredictionController::class, 'updateOrCreatePredictionWeekDay']);
Route::post('/createPredictionMonthDay', [PredictionController::class, 'updateOrCreatePredictionMonthDay']);
Route::post('/createPredictionAnHour', [PredictionController::class, 'updateOrCreatePredictionAnHour']);

// Optionally, you can define routes to retrieve predictions
Route::get('/getWaterTankPredictionDayHour/{channel_id}', [PredictionController::class, 'getWaterTankPredictionDayHour']);
Route::get('/getWaterTankPredictionWeekDay/{channel_id}', [PredictionController::class, 'getWaterTankPredictionWeekDay']);
Route::get('/getWaterTankPredictionMonthDay/{channel_id}', [PredictionController::class, 'getWaterTankPredictionMonthDay']);
Route::get('/getWaterTankPredictionAnHour/{channel_id}', [PredictionController::class, 'getWaterTankPredictionAnHour']);
// Route without auth:api middleware
Route::get('/products', function () {
    return response(['Product 1', 'Product 2', 'Product 3'], 200);
});