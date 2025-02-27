<?php

namespace App\Http\Controllers\API;

use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "first_name"       => "required",
            "last_name"        => "required",
            "email"            => "required|email",
            "password"         => "required",
            "confirm_password" => "required|same:password"
        ]);

        if ($validator->fails()) {
            return response()->json([
                "status"  => 400,
                "message" => "Validation error",
                "data"    => $validator->errors()->all()
            ]);
        }

        $user = User::create([
            "first_name" => $request->first_name,
            "last_name"  => $request->last_name,
            "email"      => $request->email,
            "password"   => bcrypt($request->password),
        ]);

        $response            = [];
        $response["token"]   = $user->createToken("IKRAM_TOKEN")->accessToken;
        $response["user_id"] = $user->id;
        $response["user"]    = $user->first_name;
        $response["email"]   = $user->email;


        return response()->json([
            "status"  => 200,
            "message" => "User Registered!",
            "data"    => $response
        ]);
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "email"            => "required|email",
            "password"         => "required",
        ]);

        if ($validator->fails()) {
            return response()->json([
                "status"  => 400,
                "message" => "Validation error",
                "data"    => $validator->errors()->all()
            ]);
        }

        if (Auth::attempt(["email" => $request->email, "password" => $request->password])) {

            $user = Auth::user();

            $response            = [];
            $response["token"]   = $user->createToken("IKRAM_TOKEN")->accessToken;
            $response["user_id"] = $user->id;
            $response["user"]    = $user->first_name;
            $response["email"]   = $user->email;

            return response()->json([
                "status"  => 200,
                "message" => "User Authenticated Successfully!",
                "data"    => $response
            ]);
        }

        //If user credentials are incorrect
        return response()->json([
            "status"  => 400,
            "message" => "Invalid Credentials",
            "data"    => null
        ]);
    }

    public function updateUser(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            "first_name" => "sometimes|required",
            "last_name"  => "sometimes|required",
            "email"      => "sometimes|required|email|unique:users,email," . $id,
            "password"   => "sometimes|required",
        ]);

        if ($validator->fails()) {
            return response()->json([
                "status"  => 400,
                "message" => "Validation error",
                "data"    => $validator->errors()->all()
            ]);
        }

        $user = User::find($id);

        if (!$user) {
            return response()->json([
                "status"  => 404,
                "message" => "User not found"
            ]);
        }

        $user->update([
            "first_name" => $request->first_name ?? $user->first_name,
            "last_name"  => $request->last_name ?? $user->last_name,
            "email"      => $request->email ?? $user->email,
            "password"   => $request->password ? bcrypt($request->password) : $user->password,
        ]);

        return response()->json([
            "status"  => 200,
            "message" => "User Updated Successfully!",
            "data"    => $user
        ]);
    }

    public function deleteUser($id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json([
                "status"  => 404,
                "message" => "User not found"
            ]);
        }

        $user->delete();

        return response()->json([
            "status"  => 200,
            "message" => "User deleted successfully"
        ]);
    }
}
