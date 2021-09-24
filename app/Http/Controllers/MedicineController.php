<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class MedicineController extends Controller
{
    //

    public function medicineList(Request $request) {
        return response("error-medecine-list", 401);
    }

    public function medicineAdd(Request $request) {
        $rule = [
            'product_code' => 'required|string',
            'medicine_name' => 'required|string',
            'grams' => 'numeric',
            'description' => 'string',
            'quantity' => 'required|numeric',
        ];

        $valid = Validator::make($request->all(), $rule);

        if ($valid->fails()) {
            return response($valid->errors(), 500);
        }

        return response()->json($request->all());
    }
    
}
