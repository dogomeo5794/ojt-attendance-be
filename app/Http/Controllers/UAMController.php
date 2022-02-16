<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

use App\UamUniqueCode;
use App\User;

class UAMController extends Controller
{
  public function __construct()
  {
    // $this->middleware('auth:api');
  }

  public function generateCode(Request $request)
  {
    $rule = [
			'user_id_numbers.*.clinic_user_id' => 'required|distinct|unique:uam_unique_code,clinic_user_id'
		];

  	$valid = Validator::make($request->all(), $rule);

  	if ($valid->fails()) {
  		return response($valid->errors(), 500);
  	}

    $id_list = $request->input('user_id_numbers');
    $collectedValues = array();
    foreach($id_list as $key=>$value){
      array_push(
        $collectedValues, 
        array(
          'unique_code' => strtoupper(Str::random(16)),
          'clinic_user_id' => $value['clinic_user_id'],
          'created_at' => Carbon::now()
        )
      );
    }

    if ($uam = UamUniqueCode::insert($collectedValues)) {
      return  response()->json($collectedValues);
    }
    
    return  response("saving error.", 500);
  }

  public function getGeneratedCode(Request $request)
  {
    // return $request->input("");
    $per_page = $request->input("per_page")??5;
    $generateCode = UamUniqueCode::orderBy('created_at', 'desc')
      ->orderBy('id', 'desc')->paginate($per_page);
    return  response()->json($generateCode);
  }

  public function collectUsers(Request $request)
  {
    $per_page = $request->input("per_page")??5;
    $user_list = User::with(["user_information", "staff_information", "user_profile_picture", "user_details"])
      ->where("role", "!=", "uam-admin")
      ->orderBy('created_at', 'desc')->paginate($per_page);

    // $user_list = User::orderBy('created_at', 'desc')->paginate(5);
    return  response()->json($user_list);
  }

}
