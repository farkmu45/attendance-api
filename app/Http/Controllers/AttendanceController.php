<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;

class AttendanceController extends Controller
{
    public function index()
    {
        return Attendance::latest()->paginate(10);
    }

    public function store(Request $request)
    {
        // Check if there's any attendance
        $userId = auth()->user()->id;
        $type = null;

        $attendanceExist = Attendance::where('user_id', $userId)
            ->where('type', 'IN')
            ->whereDate('time', now())
            ->first();

        if ($attendanceExist) {
            return response()->json([
                'error' => 'Not authorized',
                'code' => 'ATTENDANCE_EXIST',
            ], 403);
        }

        // Attendance check passed, proceed to face recognition if available
        if ($request->query('face_recognition', true)) {
            $request->validate([
                'picture' => 'required|image|max:2048',
            ]);

            $filePath = $request->file('picture')->store('public/attendances');
            $filePath = asset('storage/'.str_replace('public/', '', $filePath));

            $response = Http::post('https://farkmu45-attendance-api.hf.space', [
                'image' => $filePath,
                'target_image' => asset('storage/'.auth()->user()->picture),
            ]);

            $response = json_decode($response->body(), true);

            if (! $response['result']) {
                return response()->json([
                    'error' => 'Face not recognized',
                    'code' => 'NOT_RECOGNIZED',
                ], 403);
            }
        }

        $data = [
            'type' => $type,
            'time' => now(),
            'user_id' => auth()->user()->id,
        ];

        return Attendance::create($data);
    }

    public function show(Attendance $attendance)
    {
        return $attendance;
    }

    public function report(User $user)
    {
        $attendances = Attendance::where('user_id', $user->id)
            ->whereDate('time', '>=', now()->startOfMonth())
            ->whereDate('time', '<', now()->endOfMonth())
            ->get();

        return view('report', compact('attendances', 'user'));
    }
}
