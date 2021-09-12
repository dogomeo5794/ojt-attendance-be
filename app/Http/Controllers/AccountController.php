<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

use App\UamUniqueCode;
use App\Account;
use App\User;
use App\StaffInformation;
use App\UserInformation;
use Mockery\Generator\StringManipulation\Pass\Pass;

class AccountController extends Controller
{
  public function __construct()
  {
    // $this->middleware('auth:api', ['except' => ['login', 'userLogin', 'staffLogin', 'completeReg', 'validateInitReg']]);
  }

  public function validateInitReg(Request $request)
  {
    $rule = [
      'user_id' => 'required|exists:uam_unique_code,clinic_user_id',
      'unique_code' => 'required|exists:uam_unique_code,unique_code',
    ];
    $valid = Validator::make($request->all(), $rule);
    if ($valid->fails()) {
      return response($valid->errors(), 404);
    }

    $validatedReg = UamUniqueCode::where([
      ["unique_code", $request->input("unique_code")],
      ["clinic_user_id", $request->input("user_id")],
    ])->first();
    if (!$validatedReg) {
      return  response($request->all(), 404);
    }
    if ($validatedReg->status === 'used') {
      return response("Credentials is already used.", 406);
    }
    return  response()->json($validatedReg);
  }

  public function completeReg(Request $request)
  {
    $rule = [
      'clinic_user_id' => 'required|unique:user_information,clinic_user_id|exists:uam_unique_code,clinic_user_id',
      'unique_code' => 'required|exists:uam_unique_code,unique_code',
      'email' => 'required|unique:users,email',
      'first_name' => 'required|string',
      'middle_name' => 'required|string',
      'last_name' => 'required|string',
      'birthday' => 'required',
      'contact_no' => 'required',
      'address' => 'required',
      'role' => 'required|in:uam-admin,clinic-staff,user',
      'user_group' => 'required|in:admin,staff,teacher,student',
      'password' => 'min:6|required_with:password_confirmation|same:password_confirmation',
      'password_confirmation' => 'min:6'
    ];

    $valid = Validator::make($request->all(), $rule);

    if ($valid->fails()) {
      return response($valid->errors(), 500);
    }

    $password = hash('sha256', $request->input('password') . $request->input('clinic_user_id'));

    $user = User::create([
      'email' =>  $request->input('email'),
      'role' => $request->input('role'),
      'user_type' => $request->input('user_group'),
      'password' => Hash::make($password),
    ]);

    $user_info = $user->user_information()->create([
      "clinic_user_id" => $request->input("clinic_user_id"),
      "firstname" => $request->input("first_name"),
      "middlename" => $request->input("middle_name"),
      "lastname" => $request->input("last_name"),
      "birthday" => $request->input("birthday"),
      "contact" => $request->input("contact_no"),
      "address" => $request->input("address"),
    ]);

    $unique_code = UamUniqueCode::where([
      ['clinic_user_id', $request->input('clinic_user_id')],
      ['unique_code', $request->input('unique_code')],
    ])->first();

    $unique_code->status = "used";
    $unique_code->save();

    return response()->json($user_info->with('account')->get());
  }

  public function createStaffAccount(Request $request)
  {
    $rule = [
      'company_id' => 'required|unique:staff_information,company_id',
      'email' => 'required|unique:users,email',
      'first_name' => 'required|string',
      'middle_name' => 'required|string',
      'last_name' => 'required|string',
      'birthday' => 'required',
      'contact_no' => 'required',
      'address' => 'required',
      'role' => 'required|in:uam-admin,clinic-staff,user',
      'user_group' => 'required|in:admin,staff,teacher,student',
      'password' => 'min:6|required_with:password_confirmation|same:password_confirmation',
      'password_confirmation' => 'min:6'
    ];

    if ($request->input("role")==='uam-admin'&&$request->input('user_group')==='admin') {
      // $rule['_token'] = "required"; // require it if token already fixed
      $rule['unique_key'] = "required|in:uamadmin-unique-2021-001";
      $rule['reference_id'] = "required|in:ciis-team-2021-001";
    }

    // return response($rule, 500);

    $valid = Validator::make($request->all(), $rule);

    if ($valid->fails()) {
      return response($valid->errors(), 500);
    }

    $password = hash('sha256', $request->input('company_id'));

    $user = User::create([
      'email' =>  $request->input('email'),
      'role' => $request->input('role'),
      'user_type' => $request->input('user_group'),
      'password' => Hash::make($password),
    ]);

    $staff_info = $user->staff_information()->create([
      "company_id" => $request->input("company_id"),
      "firstname" => $request->input("first_name"),
      "middlename" => $request->input("middle_name"),
      "lastname" => $request->input("last_name"),
      "birthday" => $request->input("birthday"),
      "contact" => $request->input("contact_no"),
      "address" => $request->input("address"),
    ]);

    return response()->json($staff_info->with('account')->get());
  }

  public function userLogin(Request $request)
  {
    $rule = [
      'username' => 'required',
      'password' => 'required',
    ];

    $valid = Validator::make($request->all(), $rule);

    if ($valid->fails()) {
      return response($valid->errors(), 500);
    }

    $userInfo = UserInformation::where("clinic_user_id", $request->input("username"))
      ->with('account')->first();

    if (!$userInfo) {
      return  response($request->all(), 404);
    }

    $password = hash('sha256', $request->input('password') . $userInfo->clinic_user_id);

    if ($token = $this->guard()->attempt([
      "password" => $password,
      "email" => $userInfo->account->email
    ])) {
      // return $this->respondWithToken($token);
      return response()->json([
        'user' => $userInfo,
        'access_token' => $token,
        'token_type' => 'bearer',
        'expires_in' => $this->guard()->factory()->getTTL() * 60
      ]);
    }

    return response()->json(['error' => 'Unauthorized'], 401);

  }

  public function staffLogin(Request $request)
  {
    $rule = [
      'username' => 'required',
      'password' => 'required',
      'login_as' => 'required|in:uam-admin,staff'
    ];

    $where = [
      ["company_id", $request->input("username")]
    ];

    if ($request->input('login_as') === 'uam-admin') {
      $rule['login_as'] = "required|in:uam-admin";
    }

    $valid = Validator::make($request->all(), $rule);

    if ($valid->fails()) {
      return response($valid->errors(), 500);
    }

    $staffInfo = StaffInformation::where([
      ["company_id", $request->input("username")]
    ])
      ->with('account')->first();

    if (!$staffInfo) {
      return  response($request->all(), 404);
    }

    $password = hash('sha256', $request->input('password'));

    if ($token = $this->guard()->attempt([
      "password" => $password,
      "email" => $staffInfo->account->email
    ])) {
      return response()->json([
        'user' => $staffInfo,
        'access_token' => $token,
        'token_type' => 'bearer',
        'expires_in' => $this->guard()->factory()->getTTL() * 60
      ]);
    }

    return response()->json(['error' => 'Unauthorized'], 401);

  }

  public function my_profile()
  {
    return response()->json($this->guard()->user());
  }

  /**
   * Log the user out (Invalidate the token)
   *
   * @return \Illuminate\Http\JsonResponse
   */
  public function logout()
  {
    $this->guard()->logout();

    return response()->json(['message' => 'Successfully logged out']);
  }

  /**
   * Refresh a token.
   *
   * @return \Illuminate\Http\JsonResponse
   */
  public function refresh()
  {
    return $this->respondWithToken($this->guard()->refresh());
  }

  /**
   * Get the token array structure.
   *
   * @param  string $token
   *
   * @return \Illuminate\Http\JsonResponse
   */
  protected function respondWithToken($token)
  {
    return response()->json([
      'access_token' => $token,
      'token_type' => 'bearer',
      'expires_in' => $this->guard()->factory()->getTTL() * 60
    ]);
  }

  /**
   * Get the guard to be used during authentication.
   *
   * @return \Illuminate\Contracts\Auth\Guard
   */
  public function guard()
  {
    return Auth::guard();
  }
}
