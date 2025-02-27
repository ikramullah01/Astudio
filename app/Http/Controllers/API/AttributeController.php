<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Attribute;
use Illuminate\Support\Facades\Validator;

class AttributeController extends Controller
{
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|unique:attributes,name',
            'type' => 'required|in:text,date,number,select',
        ]);

        if ($validator->fails()) {
            return response()->json([
                "status"  => 400,
                "message" => "Validation error",
                "data"    => $validator->errors()->all()
            ]);
        }

        $attribute = Attribute::create([
            'name' => $request->name,
            'type' => $request->type,
        ]);

        return response()->json([
            'status'  => 201,
            'message' => 'Attribute created successfully',
            'data'    => $attribute
        ], 201);
    }
}
