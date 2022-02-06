<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

use App\StudentInformation;
use App\OfficeAccount;
use App\Attendance;

class AttendanceController extends Controller
{
    public function checkingAttendance(Request $request)
    {
        $student_id =  $request->input('student_id') ?? null;
        $student_info = StudentInformation::where("school_id", $student_id)->first();
        $personnel_info = OfficeAccount::where("company_id", $request->input('personnel_id') ?? "")->first();
        $personnel_ids = $personnel_info->id;
        $datetimeToday = Carbon::now()->toDateTimeString();

        if (!$student_info or !$personnel_info) {
            return response("QR Code not found in our database! Please make sure you scan a valid QR code.", 404);
        }

        if ($personnel_info->office_details->office()->where('school_id', $student_id)->count() === 0) {
            return response("Unable to find student under your company", 404);
        }

        $fullname =  ucwords($student_info->first_name) . " " . ucwords($student_info->last_name);

        $todayDate = Carbon::today()->toDateString();

        $attendance = $student_info->attendance_list()->where('attendance_date', $todayDate)->first();

        $atten_time = [];

        if (!$attendance) {
            $time_scan = 'IN';
            if (Carbon::now()->format('A') === 'AM') {
                $atten_time['time_in_am'] = $datetimeToday;
            } else {
                $time_scan = 'IN';
                $atten_time['time_in_pm'] = $datetimeToday;
            }
            $atten_time['attendance_date'] = $datetimeToday;
            $student_info->attendance()->attach($personnel_ids, $atten_time);
        } else if ($attendance->time_out_pm !== null) {
            $dateNow = Carbon::now()->format('M. d, Y');
            return response("Attendance is already closed", 409);
        } else {
            if ($attendance->time_in_am !== null && $attendance->time_out_am === null) {
                $time_scan = 'LUNCH BREAK OUT';
                $atten_time['time_out_am'] = $datetimeToday;
            } else if ($attendance->time_out_am !== null && $attendance->time_in_pm === null) {
                $time_scan = 'LUNCH BREAK IN';
                $atten_time['time_in_pm'] = $datetimeToday;
            } else if ($attendance->time_in_pm !== null && $attendance->time_out_pm === null) {
                $time_scan = 'OUT';
                $atten_time['time_out_pm'] = $datetimeToday;
            }
            $attendance->update($atten_time);
        }

        $dateTimeNow = Carbon::now()->format('m/d/Y | h:i A');
        // return response()->json("${time_scan} - ${dateTimeNow}");
        return response()->json([
            "id" => $student_info->school_id,
            "name" => "$fullname",
            "time" => "$time_scan - $dateTimeNow"
        ]);
    }

    public function collectAttendanceByStudent(Request $request)
    {
        $student_info = StudentInformation::where("school_id", $request->input('student_id') ?? '')->first();
        if (!$student_info) {
            return response("No data found", 404);
        }

        return response()->json($student_info->attendance_list);
    }

    public function validateQRCode(Request $request)
    {
        $student = StudentInformation::where("school_id", $request->input('qrcode') ?? '');
        if ($student->count() > 0) {
            return response("valid");
        } else {
            return response('invalid', 404);
        }
    }
}
