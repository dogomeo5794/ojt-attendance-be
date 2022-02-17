<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

use App\User;
use App\OfficeAccount;
use App\OfficeDetail;
use App\AdminAccount;


use App\Account;
use App\AccountEvaluated;
use App\StaffInformation;
use App\UserInformation;
use Mockery\Generator\StringManipulation\Pass\Pass;

class AccountController extends Controller
{
    public function __construct()
    {
        // $this->middleware('auth:api', ['except' => ['login', 'userLogin', 'staffLogin', 'completeReg', 'validateInitReg']]);
    }

    public function checkFreshApp(Request $request)
    {
        $admin = User::where('user_type', 'admin')->count();
        if (!$admin) {
            return response("", 404);
        } else {
            return response("", 200);
        }
    }


    /**
     * Comapany and Auth. Personnel functions
     */

    public function searchExistingCompany(Request $request)
    {
        if ($request->input('request_from') === 'student_assign_office') {
            $param = $request->input('office_registration_id') ?? "";
            $office = OfficeDetail::where("office_registration_id", $param)
                ->orWhere("office_name", "LIKE", "%" . $param . "%")
                ->first();
        } else {
            $office = OfficeDetail::where("office_registration_id", $request->input('office_registration_id') ?? "")->first();
        }

        if (!$office) {
            return response("no office found [" . $request->input('office_registration_id') . "]", 404);
        }
        return response()->json($office);
    }

    public function authPeronnelList(Request $request)
    {
        $per_page = $request->input("per_page") ?? 5;
        $personnel_list = OfficeAccount::with(["evaluated", "office_details.office" => function ($query) {
            $query->where('duty_status', 'active');
        }])->orderBy('created_at', 'desc')->paginate($per_page);
        return  response()->json($personnel_list);
    }

    public function authPeronnelInfo(Request $request)
    {
        $id = $request->input("personnel_id") ?? null;
        $office = OfficeAccount::with(['evaluated', 'account', 'office_details'])->where('company_id', $id)->first();
        if (!$office) {
            return response("", 404);
        }
        return  response()->json($office);
    }


    public function changeAccountStatus(Request $request)
    {
        $personnel = OfficeAccount::where("company_id", $request->input('company_id') ?? '')->first();
        if (!$personnel) {
            return response("", 404);
        }

        $status = $request->input('status') ?? null;

        $evaluate = $personnel->evaluated()->first();

        if (!$evaluate) {
            $evaluated = new AccountEvaluated();
            $evaluated->action_perform_date = Carbon::now()->toDateString();
            $evaluated->action_perform = $status;
            $evaluated->office_account_id = $personnel->id;
            $evaluated->admin_account_id = $request->input('admin_id') ?? null;
            $evaluated->save();
            return response()->json("Success to ${status} account");
        }

        $evaluate->action_perform_date = Carbon::now()->toDateString();
        $evaluate->action_perform = $status;
        $evaluate->save();

        return response()->json("Success to ${status} account.");
    }


    public function registerAccount(Request $request)
    {
        $rule = [
            'region' => 'required|string',
            'province' => 'required|string',
            'city' => 'required|string',
            'barangay' => 'required|string',
            'street' => 'required|string',
            'first_name' => 'required|string',
            'middle_name' => 'required|string',
            'last_name' => 'required|string',
            'birthday' => 'required|date',
            'contact_no' => 'required|max:15',
            'email' => 'required|unique:users,email',
            'username' => 'required|unique:users,username',
            'password' => 'min:6|required_with:confirm_password|same:confirm_password',
            'confirm_password' => 'min:6',
            'role' => 'required|in:uam-admin,attendance-checker',
            'user_type' => 'required|in:admin,authorized-personnel',
            'registration_type' => 'required|in:personnel,admin',
        ];

        if ($request->input('registration_type') === 'personnel') {
            $rule["office_registration_id"] = "required";
            $rule["office_name"] = "required|string";
            $rule["is_new_company"] = "required|boolean";
            $rule["company_id"] = "required|unique:office_account,company_id";
            $rule["region"] = Rule::requiredIf(function () use ($request) {
                return $request->input('is_new_company') === true;
            });
            $rule["province"] = Rule::requiredIf(function () use ($request) {
                return $request->input('is_new_company') === true;
            });
            $rule["city"] = Rule::requiredIf(function () use ($request) {
                return $request->input('is_new_company') === true;
            });
            $rule["barangay"] = Rule::requiredIf(function () use ($request) {
                return $request->input('is_new_company') === true;
            });
            $rule["street"] = Rule::requiredIf(function () use ($request) {
                return $request->input('is_new_company') === true;
            });
            $morph_to = "App\OfficeAccount";
            $username = $request->input('username');
        } else if ($request->input('registration_type') === 'admin') {
            $morph_to = "App\AdminAccount";
            $username = $request->input('company_id');
            $rule["company_id"] = "required|unique:admin_account,company_id";
        }

        $valid = Validator::make($request->all(), $rule);

        if ($valid->fails()) {
            return response($valid->errors(), 500);
        }

        $password = hash('sha256', $request->input('password') . $request->input('company_id'));

        $user = User::create([
            'username' =>  $username,
            'email' =>  $request->input('email'),
            'role' => $request->input('role'),
            'user_type' => $request->input('user_type'),
            'password' => Hash::make($password),
            'morph_to' => $morph_to,
        ]);

        if ($request->input('registration_type') === 'personnel') {
            $office_id = $request->input("office_detail_id");

            if ($request->input('is_new_company') === true) {
                $office = OfficeDetail::create([
                    "office_registration_id" => $request->input("office_registration_id"),
                    "office_name" => $request->input("office_name"),
                    "region" => $request->input("region"),
                    "province" => $request->input("province"),
                    "city" => $request->input("city"),
                    "barangay" => $request->input("barangay"),
                    "street" => $request->input("street"),
                ]);

                $office_id = $office->id;
            }

            $user_info = $user->office_account()->create([
                "company_id" => $request->input("company_id"),
                "first_name" => $request->input("first_name"),
                "middle_name" => $request->input("middle_name"),
                "last_name" => $request->input("last_name"),
                "birthday" => $request->input("birthday"),
                "contact_no" => $request->input("contact_no"),
                "office_detail_id" => $office_id,
            ]);
        } else if ($request->input('registration_type') === 'admin') {
            $user_info = $user->admin_account()->create([
                "company_id" => $request->input("company_id"),
                "first_name" => $request->input("first_name"),
                "middle_name" => $request->input("middle_name"),
                "last_name" => $request->input("last_name"),
                "birthday" => $request->input("birthday"),
                "contact_no" => $request->input("contact_no"),
                "region" => $request->input("region"),
                "province" => $request->input("province"),
                "city" => $request->input("city"),
                "barangay" => $request->input("barangay"),
                "street" => $request->input("street"),
            ]);
        }

        // return response()->json($user_info->with('account')->get());
        return response()->json($user_info);
    }

    public function updateInformation(Request $request)
    {
        $rule = [
            "account" => "required|in:admin,personnel",
            'account_id' => 'required|exists:users,id',
            'company_id' => [
                'required',
                function ($attribute, $value, $fail) use ($request) {
                    $userInfo = 0;
                    if ($request->input("account") === 'admin') {
                        $userInfo = AdminAccount::where("company_id", $value)->count();
                    } else {
                        $userInfo = OfficeAccount::where("company_id", $value)->count();
                    }
                    if ($userInfo === 0) {
                        $fail("The company id field is not exist in our database.");
                    }
                }
            ],
        ];

        $valid = Validator::make($request->all(), $rule);

        if ($valid->fails()) {
            return response($valid->errors(), 500);
        }

        $user = User::find($request->input('account_id'));
        $user->profile = $request->input('profile');
        $user->save();

        if ($request->input("account") === 'admin') {
            $userInfo = AdminAccount::where("company_id", $request->input("company_id"))
                ->update($request->except(['account', 'profile', 'account_id']));
        } else {
            $userInfo = OfficeAccount::where("company_id", $request->input("company_id"))
                ->update($request->except(['account', 'profile', 'account_id']));
        }

        return response()->json([
            "message" => "Information updated successfully"
        ]);
    }

    public function userLogin(Request $request)
    {
        $rule = [
            'username' => 'required',
            'password' => 'required',
            'login_as' => 'required|in:admin,personnel',
        ];

        $valid = Validator::make($request->all(), $rule);

        if ($valid->fails()) {
            return response($valid->errors(), 500);
        }


        $token = null;

        if ($request->input("login_as") === 'admin') {
            $userInfo = AdminAccount::where("company_id", $request->input("username"))->with('account')->first();
        } else {
            $userInfo = OfficeAccount::whereHas("account", function ($query) use ($request) {
                $query->where("username", $request->input("username"));
            })->whereHas("evaluated", function ($query) use ($request) {
                $query->where("action_perform", "approved");
            })->with('account')->first();
        }

        if (!$userInfo) {
            return  response("Invalid credentials", 404);
        }

        $password = hash('sha256', $request->input('password') . $userInfo->company_id);

        if ($token = $this->guard()->attempt([
            "password" => $password,
            "username" =>  $request->input("username")
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
        // $rule = [
        //   'username' => 'required',
        //   'password' => 'required',
        //   'login_as' => 'required|in:uam-admin,staff'
        // ];

        // $where = [
        //   ["company_id", $request->input("username")]
        // ];

        // if ($request->input('login_as') === 'uam-admin') {
        //   $rule['login_as'] = "required|in:uam-admin";
        // }

        // $valid = Validator::make($request->all(), $rule);

        // if ($valid->fails()) {
        //   return response($valid->errors(), 500);
        // }

        // $staffInfo = StaffInformation::where([
        //   ["company_id", $request->input("username")]
        // ])
        //   ->with('account')->first();

        // if (!$staffInfo) {
        //   return  response($request->all(), 404);
        // }

        // $password = hash('sha256', $request->input('password'));

        // if ($token = $this->guard()->attempt([
        //   "password" => $password,
        //   "email" => $staffInfo->account->email
        // ])) {
        //   return response()->json([
        //     'user' => $staffInfo,
        //     'access_token' => $token,
        //     'token_type' => 'bearer',
        //     'expires_in' => $this->guard()->factory()->getTTL() * 60
        //   ]);
        // }

        // return response()->json(['error' => 'Unauthorized'], 401);

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
