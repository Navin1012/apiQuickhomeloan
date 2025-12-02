<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use App\Models\PersonalAccessToken;
use Illuminate\Support\Str;
  use Illuminate\Support\Facades\DB;

class RegisteredUserController extends Controller
{
    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */

public function store(Request $request)
{
    DB::beginTransaction();

    try {
        $request->validate([
            'full_name'      => ['required', 'string', 'max:255'],
            'channel_name'   => ['required', 'string', 'max:255'],
            'channel_url'    => ['required', 'url'],
            'email'          => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'address'        => ['required', 'string', 'max:500'],
            'mobile_number'  => ['required', 'digits_between:10,15'],
            'password'       => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        
        $user = User::create([
            'full_name'     => $request->full_name,
            'channel_name'  => $request->channel_name,
            'channel_url'   => $request->channel_url,
            'email'         => $request->email,
            'address'       => $request->address,
            'mobile_number' => $request->mobile_number,
            'password'      => Hash::make($request->password),
        ]);

        $token = Str::random(60);

        PersonalAccessToken::create([
            'user_id' => (string) $user->_id,
            'name'    => 'api-token',
            'token'   => hash('sha256', $token),
        ]);

        DB::commit(); 

        return response()->json([
            'success' => true,
            'message' => 'User registered successfully',
            'user'    => $user,
            'token'   => $token,
        ], 201);

    } catch (\Illuminate\Validation\ValidationException $e) {

        DB::rollBack();
        return response()->json([
            'success' => false,
            'message' => 'Validation Failed',
            'errors'  => $e->errors(),
        ], 422);

    } catch (\Throwable $e) {

        DB::rollBack(); 
        return response()->json([
            'success' => false,
            'message' => 'Server Error',
            'error'   => $e->getMessage(),
        ], 500);
    }
}


}
