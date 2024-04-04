<?php

namespace App\Http\Controllers;

use App\Models\WaterParam;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use App\Models\Channel;
use Illuminate\Support\Facades\Http; // Import the Channel model
use Illuminate\Support\Facades\Auth;

class ChannelsController extends Controller
{
    public function index()
    {
        // Retrieve all channels from the database
        $channels = Channel::all();

        return response()->json($channels);
    }


    public function getAllChannels()
    {
        $user = Auth::user();
        $user->load('channels.waterparams');

        return response()->json([
            'user' => $user,
        ]);
    }

    public function getChannelById($channelId)
    {
        // Retrieve the authenticated user
        $user = Auth::user();

        // Load the user's channels and related waterparams
        $user->load('channels.waterparams');

        // Find the channel by its ID
        $channel = $user->channels->find($channelId);

        if (!$channel) {
            // Channel not found, return an error response
            return response()->json([
                'error' => 'Channel not found',
            ], 404); // You can use a different HTTP status code if needed
        }

        // Return the channel and its related waterparams
        return response()->json([
            'channel' => $channel,
        ]);
    }


    public function deleteChannelById($channelId)
    {
        // Retrieve the authenticated user
        $user = Auth::user();
    
        // Find the channel by its ID
        $channel = $user->channels->find($channelId);
    
        if (!$channel) {
            // Channel not found, return an error response
            return response()->json([
                'error' => 'Channel not found',
            ], 404); // You can use a different HTTP status code if needed
        }
    
        // Delete the channel and its related waterparams
        $channel->delete();
    
        // Return a success message
        return response()->json([
            'message' => 'Channel deleted successfully',
        ]);
    }
    
    public function store(Request $request)
    {
        $user = Auth::user();
        // Validate the incoming request
        $request->validate([
            'channel_id' => 'required|numeric',
            // Add other validation rules for your fields
        ]);
        $channelId = $request->input('channel_id');
        // Call ThingSpeak API to get data for the provided channel_id
        $apiUrl = "https://api.thingspeak.com/channels/{$channelId}/feeds.json?results=1&timezone=Asia%2FKuala_Lumpur";
        try {
            // Attempt to fetch data from the API using file_get_contents
            $apiResponse = file_get_contents($apiUrl);

            if ($apiResponse === false) {
                // Handle file_get_contents error
                return response()->json([
                    'error' => 'Unable to fetch data from ThingSpeak API',
                ], 400);
            }
            // Parse the JSON response
            $apiData = json_decode($apiResponse, true);
            // Check if the channel name matches "Aquaculture"
            if (isset($apiData['channel']) && $apiData['channel']['name'] === 'Aquaculture') {
                // Create a new channel record in the database
                $channel = $user->channels()->create([
                    'channel_id' => $apiData['channel']['id'],
                    // Assign other fields
                ]);
                // Additional logic to create waterparams entries using the API result
                foreach ($apiData['channel'] as $key => $value) {
                    if (strpos($key, 'field') === 0 && $value) {
                        $fieldNumber = substr($key, 5); // Extract field number
                        $chartTitle = ucfirst($value); // Convert to title case

                        $channel->waterparams()->create([
                            'water_parameter' => $chartTitle,
                            'chart_id' => $key,
                            'chart_title' => $chartTitle,
                            'field_id' => $key,
                            'min_level' => 0,
                            'max_level' => 10,
                            'min_safe' => 4,
                            'max_safe' => 8,
                            'normal_color' => '#339933',
                            'warning_color' => 'red',
                            'unit' => 'unit',
                            'line_graph_webview_link' => '',
                            'gauge_webview_link' => '',
                        ]);
                    }
                }
                $user->load('channels.waterparams');
                return response()->json([
                    'message' => 'Channel created successfully',
                    'user' => $user,
                ], 201);
            } else {
                // Channel name doesn't match
                return response()->json([
                    'error' => 'Channel ID not found',
                ], 400);
            }
        } catch (\Exception $e) {
            // Handle exceptions here (e.g., network errors)
            return response()->json([
                'error' => 'Channel ID not found',
            ], 400);
        }
    }

    // Add more controller methods as needed (update, delete, show, etc.)
}