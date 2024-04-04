<?php

namespace App\Http\Controllers;

use App\Models\WaterParam;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class WaterParamsController extends Controller
{
    //
    public function index(Request $request){
        $params = WaterParam::all();
        return view('dashboard')->with(['params' => $params]);
    }
    public function byUserId(Request $request){
        $channelIds = Auth::user()->channels->pluck('channel_id');
        $params = WaterParam::whereIn('channel_id', $channelIds)->get();
        
        return  json_encode($params);
    }
    public function getChannelById($channelId)
    {
        $user = Auth::user();
        $channel = $user->channels()->with('waterparams')->findOrFail($channelId);

        return response()->json([
            'channel' => $channel,
        ]);
    }
    public function getWaterParamById($channelId){
        $user = Auth::user();
        $channel = $user->channels()->findOrFail($channelId);
        $waterparams = $channel->waterparams()->get();
        return response()->json([
            'channel' => $waterparams,
        ]);
    }
    public function show(Request $request){
        $params = WaterParam::find($id);
        return view('show')->with(['params' => $params]);
    }
    public function edit(Request $request){
        $params = WaterParam::find($id);
        return view('edit', ['params' => $params]);
    }
    public function update(Request $request){
        $user = Auth::user();
        $channelId = $request->channel_id;
        $waterParameter = $request->water_parameter;
        $chartId = $request->chart_id;
        
        // return response()->json(['line_graph_webview_link' => $line_graph_webview_link, 'gauge_webview_link'=>$gauge_webview_link], 401);

        // Find the specific water parameter settings for the given channel, water parameter, and chart ID
        
        $params = WaterParam::where('channel_id', $channelId)
            ->where('water_parameter', $waterParameter)
            ->where('chart_id', $chartId)
            ->first();
        
        if ($params) {
            $params->chart_title = $request->chart_title;
            $params->show_graph = 1;
            $params->field_id = $request->field_id;
            $params->min_level = $request->min_level;
            $params->max_level = $request->max_level;
            $params->normal_color = $request->normal_color;
            $params->warning_color = $request->warning_color;
            $params->min_safe = $request->min_safe;
            $params->max_safe = $request->max_safe;
            $params->unit = $request->unit;
            $params->line_graph_webview_link = $request->line_graph_webview_link ?? "";;
            $params->gauge_webview_link = $request->gauge_webview_link ?? "";;

            if ($params->save()) {
                return response()->json(['message' => 'Changes saved successfully.','params' => $params]);
            }
            return response()->json(['message' => 'Failed to save changes.'], 422);
        }
    
        return response()->json(['message' => 'Water parameter settings not found.'], 401);
    }
    public function create(Request $request){
        return view('add');
    }
    public function store(Request $request){
        $params = new WaterParam;
        $params->chart_id = $request->chart_id;
        $params->chart_title = $request->chart_title;
        $params->user_id = Auth::user()->id;
        if ($params->save()) {
            return view('show', ['params' => $params]);
        }

        return; // 422
    }
    public function delete(Request $request){
        $params = WaterParam::where('user_id', Auth::user()->id)->where('id', $id)->first();
        if ($params) {
            $params->delete();
            return view('index');
        }
        return; // 404
    }
}
