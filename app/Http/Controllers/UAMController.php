<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

use App\UamUniqueCode;

class UAMController extends Controller
{
  public function generateCode(Request $request)
  {
    $rule = [
			'user_id_numbers.*.user_system_id' => 'required|distinct|unique:uam_unique_code,user_system_id'
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
          'user_system_id' => $value['user_system_id'],
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

  public function validateInitReg(Request $request)
  {
    $rule = [
			'user_id' => 'required',
			'unique_code' => 'required',
		];
    $valid = Validator::make($request->all(), $rule);
  	if ($valid->fails()) {
  		return response($valid->errors(), 500);
  	}

    $validatedReg = UamUniqueCode::where([
      ["unique_code", $request->input("unique_code")],
      ["user_system_id", $request->input("user_id")],
    ])->first();
    if (!$validatedReg) {
      return  response($request->all(), 404);
    }
    return  response()->json($validatedReg);
  }

}
