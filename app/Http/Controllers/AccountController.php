<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

use App\UamUniqueCode;
use App\Account;
use App\User;
use Mockery\Generator\StringManipulation\Pass\Pass;

class AccountController extends Controller
{
 
    // '', '', '', '',
    //     'street', 'barangay', 'city', 'province', 'region',
    //     'email', 'password', 'role', 'user_type',

        // complete registration
  public function completeReg(Request $request) {
    $rule = [
      'user_system_id' => 'required|unique:users,user_system_id|exists:uam_unique_code,user_system_id',
      'unique_code' => 'required|exists:uam_unique_code,unique_code',
			'email' => 'required|unique:users,email',
			'firstname' => 'required|string',
			'middlename' => 'required|string',
			'lastname' => 'required|string',
			'role' => 'required|in:uam-admin,clinic-staff,user',
			'user_type' => 'required|in:admin,staff,teacher,student',
      'password' => 'min:6|required_with:password_confirmation|same:password_confirmation',
      'password_confirmation' => 'min:6'
		];

  	$valid = Validator::make($request->all(), $rule);

  	if ($valid->fails()) {
  		return response($valid->errors(), 500);
  	}

    $password = hash('sha256', $request->input('password').$request->input('user_system_id'));

  	$user = new User;
  	$user->user_system_id = $request->input('user_system_id');
  	$user->email = $request->input('email');
    $user->firstname = $request->input('firstname');
  	$user->middlename = $request->input('middlename');
  	$user->lastname = $request->input('lastname');
  	$user->role = $request->input('role');
  	$user->user_type = $request->input('user_type');
  	$user->password = Hash::make($password);
    $user->save();

    return response()->json($user);
  }

  public function userLogin(Request $request) {
    $rule = [
			'username' => 'required',
      'password' => 'required',
		];

  	$valid = Validator::make($request->all(), $rule);

  	if ($valid->fails()) {
  		return response($valid->errors(), 500);
  	}

    $userInfo = User::where("user_system_id", $request->input("username"))
      ->orWhere("email", $request->input("username"))
      ->first();

    if (!$userInfo) {
      return  response($request->all(), 404);
    }

    $password = hash('sha256', $request->input('password').$userInfo->user_system_id);

    if (!Hash::check($password, $userInfo->password)) {
      return  response($request->all(), 404);
    }

    return response()->json($userInfo);
  }
}
