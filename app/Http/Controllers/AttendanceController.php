<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;

class AttendanceController extends Controller
{
    public function index()
    {
        Attendance::all();
    }

    public function store(Request $request)
    {
        // Check if there's any attendance
        $userId = auth()->user()->id;
        $type = null;

        $attendanceIn = Attendance::where('user_id', $userId)
            ->where('type', 'IN')
            ->whereDate('time', now())
            ->first();

        $attendanceOut = Attendance::where('user_id', $userId)
            ->where('type', 'OUT')
            ->whereDate('time', now())
            ->first();

        if ($attendanceIn) {
            $type = 'OUT';
        } else {
            $type = 'IN';
        }

        if ($attendanceOut) {
            $type = null;
        }

        if (!$type) {
            return response()->json([
                'error' => 'Not authorized',
                'code' => 'ATTENDANCE_EXISTS'
            ], 403);
        }

        // Attendance check passed, proceed to face recognition if available
        if ($request->query('face_recognition', true)) {
            $request->validate([
                'picture' => 'required|image|max:2048',
            ]);

            $filePath = $request->file('picture')->store('public/attendances');
            $filePath = asset('storage/' . str_replace('public/', '', $filePath));

            $response = Http::post('https://farkmu45-attendance-api.hf.space', [
                'image' => $filePath,
                'target_image' => asset('storage/' . auth()->user()->picture),
            ]);

            $response = json_decode($response->body(), true);

            return [$response, $filePath,  asset('storage/' . auth()->user()->picture)];

            if (!$response['result']) {
                return response()->json([
                    'error' => 'Face not recognized',
                    'code' => 'NOT_RECOGNIZED'
                ], 403);
            }
        }

        // Face recognition passed, check if the user attends on time
        $deviate = false;
        $startTime = Carbon::createFromTimeString(config('app.start_time'));
        $endTime = Carbon::createFromTimeString(config('app.end_time'));

        if (now() > $startTime && $type == 'IN') {
            $deviate = true;
        } else if (now() < $endTime && $type == 'OUT') {
            $deviate = true;
        }

        $data = [
            'type' => $type,
            'time' => now(),
            'is_deviate' => $deviate,
            'user_id' => auth()->user()->id
        ];

        return Attendance::create($data);
    }

    public function show(Attendance $attendance)
    {
        return $attendance;
    }
}
