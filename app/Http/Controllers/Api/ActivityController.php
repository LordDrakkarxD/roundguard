<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Activitylog\Models\Activity;

class ActivityController extends Controller
{
    public function activityLog()
    {
        $activities = Activity::with('causer:id,name')
            ->latest()
            ->limit(50)
            ->get()
            ->map(function ($activity) {
                return [
                    'id' => $activity->id,
                    'description' => $activity->description,
                    'event' => $activity->event,
                    'subject_type' => class_basename($activity->subject_type),
                    'causer' => $activity->causer?->name,
                    'created_at' => $activity->created_at?->format('d/m/Y H:i'),
                ];
            });

        return response()->json($activities);
    }
}
