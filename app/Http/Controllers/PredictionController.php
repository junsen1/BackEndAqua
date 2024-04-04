<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\WaterTankPredictionDayHour;
use App\Models\WaterTankPredictionWeekDay;
use App\Models\WaterTankPredictionMonthDay;
use App\Models\WaterTankPredictionAnHour;

class PredictionController extends Controller
{
    public function getWaterTankPredictionDayHour($channel_id)
    {
        $predictions = WaterTankPredictionDayHour::where('channel_id', $channel_id)->get();
        return response()->json($predictions);
    }

    // Retrieve WaterTankPredictionWeekDay records based on channel ID
    public function getWaterTankPredictionWeekDay($channel_id)
    {
        $predictions = WaterTankPredictionWeekDay::where('channel_id', $channel_id)->get();
        return response()->json($predictions);
    }

    // Retrieve WaterTankPredictionMonthDay records based on channel ID
    public function getWaterTankPredictionMonthDay($channel_id)
    {
        $predictions = WaterTankPredictionMonthDay::where('channel_id', $channel_id)->get();
        return response()->json($predictions);
    }

    // Retrieve WaterTankPredictionAnHour records based on channel ID
    public function getWaterTankPredictionAnHour($channel_id)
    {
        $predictions = WaterTankPredictionAnHour::where('channel_id', $channel_id)->get();
        return response()->json($predictions);
    }
    public function updateOrCreatePredictionDayHour(Request $request)
    {
        // Validate the request data
        $request->validate([
            'channel_id' => 'required|string',
            'water_parameter' => 'required|string',
            'value' => 'required|numeric',
        ]);

        $channelId = $request->input('channel_id');
        $waterParameter = $request->input('water_parameter');
        $value = $request->input('value');

        // Find a record in the water_tank_prediction table with the same channel_id and water_parameter
        $prediction = WaterTankPredictionDayHour::where('channel_id', $channelId)
            ->where('water_parameter', $waterParameter)
            ->first();

        if ($prediction) {
            // If a matching record is found, update its value
            $prediction->update(['value' => $value]);
        } else {
            // If no matching record is found, create a new record
            WaterTankPredictionDayHour::create([
                'channel_id' => $channelId,
                'water_parameter' => $waterParameter,
                'value' => $value,
            ]);
        }

        return response()->json(['message' => 'Prediction record updated or created successfully']);
    }
    public function updateOrCreatePredictionWeekDay(Request $request)
    {
        // Validate the request data
        $request->validate([
            'channel_id' => 'required|string',
            'water_parameter' => 'required|string',
            'value' => 'required|numeric',
        ]);

        $channelId = $request->input('channel_id');
        $waterParameter = $request->input('water_parameter');
        $value = $request->input('value');

        // Find a record in the water_tank_prediction table with the same channel_id and water_parameter
        $prediction = WaterTankPredictionWeekDay::where('channel_id', $channelId)
            ->where('water_parameter', $waterParameter)
            ->first();

        if ($prediction) {
            // If a matching record is found, update its value
            $prediction->update(['value' => $value]);
        } else {
            // If no matching record is found, create a new record
            WaterTankPredictionWeekDay::create([
                'channel_id' => $channelId,
                'water_parameter' => $waterParameter,
                'value' => $value,
            ]);
        }

        return response()->json(['message' => 'Prediction record updated or created successfully']);
    }
    public function updateOrCreatePredictionMonthDay(Request $request)
    {
        // Validate the request data
        $request->validate([
            'channel_id' => 'required|string',
            'water_parameter' => 'required|string',
            'value' => 'required|numeric',
        ]);

        $channelId = $request->input('channel_id');
        $waterParameter = $request->input('water_parameter');
        $value = $request->input('value');

        // Find a record in the water_tank_prediction table with the same channel_id and water_parameter
        $prediction = WaterTankPredictionMonthDay::where('channel_id', $channelId)
            ->where('water_parameter', $waterParameter)
            ->first();

        if ($prediction) {
            // If a matching record is found, update its value
            $prediction->update(['value' => $value]);
        } else {
            // If no matching record is found, create a new record
            WaterTankPredictionMonthDay::create([
                'channel_id' => $channelId,
                'water_parameter' => $waterParameter,
                'value' => $value,
            ]);
        }

        return response()->json(['message' => 'Prediction record updated or created successfully']);
    }
    public function updateOrCreatePredictionAnHour(Request $request)
    {
        // Validate the request data
        $request->validate([
            'channel_id' => 'required|string',
            'water_parameter' => 'required|string',
            'value' => 'required|numeric',
        ]);

        $channelId = $request->input('channel_id');
        $waterParameter = $request->input('water_parameter');
        $value = $request->input('value');

        // Find a record in the water_tank_prediction table with the same channel_id and water_parameter
        $prediction = WaterTankPredictionAnHour::where('channel_id', $channelId)
            ->where('water_parameter', $waterParameter)
            ->first();

        if ($prediction) {
            // If a matching record is found, update its value
            $prediction->update(['value' => $value]);
        } else {
            // If no matching record is found, create a new record
            WaterTankPredictionAnHour::create([
                'channel_id' => $channelId,
                'water_parameter' => $waterParameter,
                'value' => $value,
            ]);
        }

        return response()->json(['message' => 'Prediction record updated or created successfully']);
    }
    // public function predict(Request $request)
    // {
    //     $data = $request->input('data');
    //     $dataJson = json_encode(['data' => $data]);
    //     $response = Http::post('http://localhost:5000/predict', [
    //         'data' => $dataJson, // Send the JSON string
    //     ]);
    //     if ($response->failed()) {
    //         return response()->json(['error' => 'Failed to get a prediction from the Python server']);
    //     }

    //     return $response->json();
    // }
    public function predict(Request $request)
    {
        $data = $request->input('data');
        // // Prepare the input data as JSON
        $inputJson = json_encode($data);
        // return response()->json($inputJson);

        // // Escape the double quotes inside the JSON string
        $escapedInputJson = str_replace('"', '\"', $inputJson);
        // return response()->json($escapedInputJson);
        // // Execute Python script and capture output
        $pythonScriptPath = base_path('scripts/arima_predict.py');
        $command = "python $pythonScriptPath \"$escapedInputJson\"";
        $predictedValues = shell_exec($command);
        $predictedValues = json_decode($predictedValues, true);

    return response()->json(['prediction' => $predictedValues]);
    }
}

