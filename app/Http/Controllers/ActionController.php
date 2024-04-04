<?php

namespace App\Http\Controllers;

use App\Models\Action;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use DateTime;
use Carbon\Carbon;

class ActionController extends Controller
{
    public function calculateDurationForCurrentMonth(Request $request, $channel_id)
    {
        $title = $request->input('title');
       // Retrieve authenticated user's channel IDs
        $userChannelIds = Auth::user()->channels->pluck('channel_id');

        // Check if the provided channel_id is within the user's channels
        if ($userChannelIds->contains($channel_id)) {
            $now = Carbon::now();
            $startOfMonth = $now->copy()->startOfMonth();
            $endOfMonth = $now->copy()->endOfMonth();

            // Calculate total duration of an action in the current month
            $currentMonthActions = Action::where('channel_id', $channel_id)
                ->where('title', $title)
                ->whereBetween('start_time', [$startOfMonth, $endOfMonth])
                ->get();

            $totalDurationCurrentMonth = 0;

            foreach ($currentMonthActions as $action) {
                $startTime = Carbon::parse($action->start_time);
                $endTime = Carbon::parse($action->end_time);

                $duration = $endTime->diffInSeconds($startTime);
                $totalDurationCurrentMonth += $duration;
            }

            // Calculate total duration of an action in months excluding the current month
            $totalDurationOtherMonths = 0;
            $months = collect([]);
            $currentMonth = Carbon::now()->month;

            // Get actions for each month (except the current month)
            for ($i = 1; $i <= 12; $i++) {
                if ($i !== $currentMonth) {
                    $startOfMonth = Carbon::create(null, $i, 1)->startOfMonth();
                    $endOfMonth = Carbon::create(null, $i, 1)->endOfMonth();

                    $actions = Action::where('channel_id', $channel_id)
                        ->where('title', $title)
                        ->whereBetween('start_time', [$startOfMonth, $endOfMonth])
                        ->get();

                    $totalDuration = 0;

                    foreach ($actions as $action) {
                        $startTime = Carbon::parse($action->start_time);
                        $endTime = Carbon::parse($action->end_time);

                        $duration = $endTime->diffInSeconds($startTime);
                        $totalDuration += $duration;
                    }

                    $totalDurationOtherMonths += $totalDuration;
                }
            }

            // Calculate average total duration of an action in months excluding the current month
            $averageTotalDurationOtherMonths = $totalDurationOtherMonths / ($months->count() ?: 1);

            // Calculate average duration to carry out the action
            $averageDurationToCarryOut = $totalDurationCurrentMonth / ($currentMonthActions->count() ?: 1);

            // Calculate total duration of an action in the last month
            $lastMonth = Carbon::now()->subMonth();
            $lastMonthStart = $lastMonth->copy()->startOfMonth();
            $lastMonthEnd = $lastMonth->copy()->endOfMonth();

            $lastMonthActions = Action::where('channel_id', $channel_id)
                ->where('title', $title)
                ->whereBetween('start_time', [$lastMonthStart, $lastMonthEnd])
                ->get();

            $totalDurationLastMonth = 0;

            foreach ($lastMonthActions as $action) {
                $startTime = Carbon::parse($action->start_time);
                $endTime = Carbon::parse($action->end_time);

                $duration = $endTime->diffInSeconds($startTime);
                $totalDurationLastMonth += $duration;
            }

            $result = [
                'total_duration_current_month' => round($totalDurationCurrentMonth / 3600, 1),
                'average_total_duration_other_months' => round($averageTotalDurationOtherMonths / 3600, 1),
                'average_duration_to_carry_out' => round($averageDurationToCarryOut / 3600, 1),
                'total_duration_last_month' => round($totalDurationLastMonth / 3600, 1),
            ];

            return response()->json($result);
        } else {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
    }
    public function calculateTotalAndAverageDurationForCurrentMonth(Request $request, $channel_id)
    {
        $title = $request->input('title');
        // Retrieve authenticated user's channel IDs
        $userChannelIds = Auth::user()->channels->pluck('channel_id');

        // Check if the provided channel_id is within the user's channels
        if ($userChannelIds->contains($channel_id)) {
            $now = Carbon::now();
            $startOfMonth = $now->copy()->startOfMonth();
            $endOfMonth = $now->copy()->endOfMonth();

            $actions = Action::where('channel_id', $channel_id)
                ->where('title', $title)
                ->whereBetween('start_time', [$startOfMonth, $endOfMonth])
                ->orderBy('start_time')
                ->get();

            $totalDurationInSeconds = 0;

            foreach ($actions as $action) {
                $startTime = Carbon::parse($action->start_time);
                $endTime = Carbon::parse($action->end_time);

                $durationInSeconds = $endTime->diffInSeconds($startTime);
                $totalDurationInSeconds += $durationInSeconds;
            }

            $totalDurationInHours = floor($totalDurationInSeconds / 3600);
            $totalDurationInMinutes = floor(($totalDurationInSeconds % 3600) / 60);

            $averageDurationInSeconds = count($actions) > 0 ? $totalDurationInSeconds / count($actions) : 0;
            $averageDurationInMinutes = floor(($averageDurationInSeconds % 3600) / 60);

            $result = [
                'total_duration' => [
                    'hours' => $totalDurationInHours,
                    'minutes' => $totalDurationInMinutes,
                ],
                'average_duration' => [
                    'minutes' => $averageDurationInMinutes,
                ],
            ];

            return response()->json($result);
        } else {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
    }

    public function getCurrentAndLastMonthFrequency(Request $request, $channel_id)
    {
        $title = $request->input('title');
        // Retrieve authenticated user's channel IDs
        $userChannelIds = Auth::user()->channels->pluck('channel_id');

        // Check if the provided channel_id is within the user's channels
        if ($userChannelIds->contains($channel_id)) {
            // Get the start and end dates of the current and last months
            $currentMonthStart = Carbon::now()->startOfMonth();
            $currentMonthEnd = Carbon::now()->endOfMonth();
            $lastMonthStart = Carbon::now()->subMonth()->startOfMonth();
            $lastMonthEnd = Carbon::now()->subMonth()->endOfMonth();

            // Retrieve all actions with the specified channel_id and title
            $currentMonthActions = Action::where('channel_id', $channel_id)
                ->where('title', $title)
                ->whereBetween('start_time', [$currentMonthStart, $currentMonthEnd])
                ->orderBy('start_time', 'asc')
                ->get();

            // Retrieve all actions with the specified channel_id and title for the last month
            $lastMonthActions = Action::where('channel_id', $channel_id)
                ->where('title', $title)
                ->whereBetween('start_time', [$lastMonthStart, $lastMonthEnd])
                ->orderBy('start_time', 'asc')
                ->get();

            // Calculate the frequency counts
            $currentMonthFrequency = $currentMonthActions->count();
            $lastMonthFrequency = $lastMonthActions->count();
            
            // Calculate the average total frequency
            $allActions = Action::where('channel_id', $channel_id)
                ->where('title', $title)
                ->orderBy('start_time', 'asc')
                ->get();
            $totalFrequency = $allActions->count();
            $averageTotalFrequency = $totalFrequency / $allActions->groupBy(function ($date) {
                return Carbon::parse($date->start_time)->format('Y-m');
            })->count();

            $result = [
                'current_month_frequency' => $currentMonthFrequency,
                'last_month_frequency' => $lastMonthFrequency,
                'average_total_frequency' => $averageTotalFrequency,
            ];

            return response()->json($result);
        } else {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
    }

    public function getLastActivityIntervals(Request $request, $channelId)
    {
        $title = $request->input('title');

        // Verify if the channel_id belongs to the authenticated user
        $user = Auth::user();
        $userChannelIds = $user->channels->pluck('channel_id');

        if (!$userChannelIds->contains($channelId)) {
            return response()->json(['error' => 'Invalid channel_id'], 403);
        }

        $lastAction = Action::where('title', $title)
            ->where('channel_id', $channelId)
            ->orderBy('start_time', 'desc')
            ->first();

        if ($lastAction) {
            $currentTime = Carbon::now();
            $lastActionTime = Carbon::parse($lastAction->start_time);
            $duration = $lastActionTime->diff($currentTime);

            $days = $duration->days;
            $hours = $duration->h;

            return response()->json([
                'days' => $days,
                'hours' => $hours,
            ]);
        } else {
            return response()->json([
                'error' => 'No actions found with the given title.',
            ], 404);
        }
    }
    public function getAverageFrequency(Request $request, $channel_id)
    {
        $title = $request->input('title');
        // Retrieve authenticated user's channel IDs
        $userChannelIds = Auth::user()->channels->pluck('channel_id');

        // Check if the provided channel_id is within the user's channels
        if ($userChannelIds->contains($channel_id)) {
            // Retrieve all actions with the specified channel_id and title
            $actions = Action::where('channel_id', $channel_id)
                ->where('title', $title)
                ->orderBy('start_time', 'asc')
                ->get();

            // Initialize an array to store monthly counts
            $monthlyCounts = [];

            // Loop through the actions to calculate monthly counts
            foreach ($actions as $action) {
                $monthYear = Carbon::parse($action->start_time)->format('Y-m');

                if (!isset($monthlyCounts[$monthYear])) {
                    $monthlyCounts[$monthYear] = 0;
                }

                $monthlyCounts[$monthYear]++;
            }

            return response()->json($monthlyCounts);
        } else {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
    }
    public function getAverageIntervals(Request $request, $channel_id)
    {
        $title = $request->input('title');
        // Retrieve authenticated user's channel IDs
        $userChannelIds = Auth::user()->channels->pluck('channel_id');

        // Check if the provided channel_id is within the user's channels
        if ($userChannelIds->contains($channel_id)) {
            // Retrieve all actions with the specified channel_id and title
            $actions = Action::where('channel_id', $channel_id)
                ->where('title', $title)
                ->orderBy('start_time', 'asc')
                ->get();

            $totalHours = 0;

            // Loop through the actions to calculate total duration in hours
            for ($i = 1; $i < count($actions); $i++) {
                $startTime = Carbon::parse($actions[$i - 1]->start_time);
                $endTime = Carbon::parse($actions[$i]->start_time);
                $durationHours = $endTime->diffInHours($startTime);

                $totalHours += $durationHours;
            }

            $averageHours = count($actions) > 1 ? $totalHours / (count($actions) - 1) : 0;

            $averageDays = floor($averageHours / 24);
            $remainingHours = $averageHours % 24;

            return response()->json([
                'average_duration_days' => $averageDays,
                'average_duration_hours' => $remainingHours,
            ]);
        } else {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
    }
    public function getActivityIntervals(Request $request, $channel_id)
    {
        $title = $request->input('title');
        // Retrieve authenticated user's channel IDs
        $userChannelIds = Auth::user()->channels->pluck('channel_id');

        // Check if the provided channel_id is within the user's channels
        if ($userChannelIds->contains($channel_id)) {
            // Retrieve all actions with the specified channel_id and title
            $actions = Action::where('channel_id', $channel_id)
                ->where('title', $title)
                ->orderBy('start_time', 'asc')
                ->get();

            $activityDurations = [];

            // Loop through the actions to calculate durations
            for ($i = 1; $i < count($actions); $i++) {
                $startTime = Carbon::parse($actions[$i - 1]->start_time);
                $endTime = Carbon::parse($actions[$i]->start_time);
                $duration = $endTime->diff($startTime);

                $activityDurations[] = [
                    'start_time' => $startTime->toDateTimeString(),
                    'end_time' => $endTime->toDateTimeString(),
                    'duration_days' => $duration->days,
                    'duration_hours' => $duration->h,
                ];
            }

            return response()->json($activityDurations);
        } else {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
    }
    public function getActivityDurations($channel_id)
    {
        // Retrieve user's channel IDs
        $userChannelIds = Auth::user()->channels->pluck('channel_id')->toArray();

        // Check if the provided channel ID is in the user's owned channels
        if (in_array($channel_id, $userChannelIds)) {
            // Retrieve actions for the specified channel
            $actions = Action::where('channel_id', $channel_id)->get();

            // Calculate activity durations
            $activityDurations = [];
            foreach ($actions as $action) {
                $startTime = new DateTime($action->start_time);
                $endTime = new DateTime($action->end_time);
                $duration = $startTime->diff($endTime);
                $activityDurations[] = [
                    'title' => $action->title,
                    'start_time' => $startTime->format('Y-m-d H:i:s'),
                    'end_time' => $endTime->format('Y-m-d H:i:s'),
                    'duration' => $duration->format('%H:%I:%S'),
                ];
            }

            return response()->json(['activity_durations' => $activityDurations]);
        } else {
            return response()->json(['message' => 'Unauthorized channel ID'], 401);
        }
    }

    public function index()
    {
        // Retrieve all actions
        $actions = Action::all();
        return response()->json($actions);
    }

    public function store(Request $request)
    {
        // Create a new action
        $action = Action::create($request->all());
        return response()->json($action, 201);
    }

    public function showByChannel($channel_id)
    {
        // Retrieve all actions with the specified channel_id
        $actions = Action::where('channel_id', $channel_id)->get();

        return response()->json($actions);
    }

    public function update(Request $request, Action $action)
    {
        // Update the action
        $action->update($request->all());
        return response()->json($action);
    }

    public function destroy(Action $action)
    {
        // Delete the action
        $action->delete();
        return response()->json(null, 204);
    }
}