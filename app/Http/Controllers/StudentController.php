<?php

namespace App\Http\Controllers;

use App\OfficeAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

use SimpleSoftwareIO\QrCode\Facades\QrCode;

use App\StudentInformation;
use App\OjtOffice;
use App\OfficeDetail;

class StudentController extends Controller
{
  public function createQrCode()
  {
    // return phpinfo();
    // return QrCode::wiFi([
    //     'encryption' => 'WPA/WEP',
    //     'ssid' => 'SSID of the network',
    //     'password' => 'Password of the network',
    //     'hidden' => 'Whether the network is a hidden SSID or not.'
    // ]);
    return QrCode::format('png')->size(250)->generate('ew0KICBzdHVkZW50X2lkOiAiUlJELTIwMjEtMDAxIiwNCn0=');
    return QrCode::size(500)
      ->generate('ItSolutionStuff.com', public_path('images/qrcode.png'));
  }

  public function createStudent(Request $request)
  {

    $rule = [
      'birthday' => 'required|date',
      'contact_no' => 'required|max:15',
      'email' => 'required|unique:student_information,email',
      'first_name' => 'required|string',
      'last_name' => 'required|string',
      'middle_name' => 'required|string',
      'student_id' => 'required|unique:student_information,school_id',
      'region' => 'required',
      'city' => 'required',
      'barangay' => '',
    ];

    $valid = Validator::make($request->all(), $rule);

    if ($valid->fails()) {
      return response($valid->errors(), 500);
    }

    $student_info = StudentInformation::create([
      "birthday" => $request->input('birthday'),
      "barangay" => $request->input('barangay'),
      "province" => $request->input('province'),
      "city" => $request->input('city'),
      "contact_no" => $request->input('contact_no'),
      "course_code" => $request->input('course_code'),
      "course_name" => $request->input('course_name'),
      "email" => $request->input('email'),
      "first_name" => $request->input('first_name'),
      "last_name" => $request->input('last_name'),
      "middle_name" => $request->input('middle_name'),
      "region" => $request->input('region'),
      "section" => $request->input('section'),
      "street" => $request->input('street'),
      "school_id" => $request->input('student_id'),
      "year_level" => $request->input('year_level'),
    ]);

    return response()->json($student_info);
  }

  public function createdStudentList(Request $request) {
    $per_page = $request->input("per_page")??5;
    if ($request->input('company_id')) {
      $office = OfficeAccount::where("company_id", $request->input('company_id'))->first();
      return response()->json($office->office_details->office()->paginate($per_page));
    }
    $user_list = StudentInformation::with("office")->orderBy('created_at', 'desc')->paginate($per_page);
    return  response()->json($user_list);
  }

  public function collectStudentInfo(Request $request) {
    $school_id = $request->input("school_id")??"";
    $user_info = StudentInformation::with(["office", "attendance_list"])->where("school_id", $school_id)->first();
    if (!$user_info) {
      return response("", 404);
    }
    return  response()->json($user_info);
  }

  public function assigningOffice(Request $request) {
    $rule = [
      'office_id' => 'required|exists:office_details,id',
      'student_id' => 'required|exists:student_information,id',
    ];

    $valid = Validator::make($request->all(), $rule);

    if ($valid->fails()) {
      return response($valid->errors(), 500);
    }

    $student_info = StudentInformation::find($request->input('student_id'));
    $office_ids = $request->input('office_id');
    $student_info->office()->attach($office_ids);

    return response()->json($student_info);
  }
}
