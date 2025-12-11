<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\PersonalAccessToken;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
class AuthenticatedSessionController extends Controller
{
    public function store(Request $request)
    {
        DB::beginTransaction();

        try {
            $request->validate([
                'email'    => 'required|email',
                'password' => 'required'
            ]);

            $user = User::where('email', $request->email)->first();

            if (!$user || !Hash::check($request->password, $user->password)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid credentials'
                ], 401);
            }

            Auth::login($user);

            PersonalAccessToken::where('user_id', (string)$user->_id)->delete();

            $token = Str::random(60);

            PersonalAccessToken::create([
                'user_id' => (string) $user->_id,
                'name'    => 'api-token',
                'token'   => hash('sha256', $token),
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Logged in successfully',
                'user'    => $user,
                'token'   => $token,
            ], 200);

        } catch (\Illuminate\Validation\ValidationException $e) {

            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Validation Failed',
                'errors'  => $e->errors()
            ], 422);

        } catch (\Throwable $e) {

            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Server Error',
                'error'   => $e->getMessage()
            ], 500);
        }
    }



    public function destroy(Request $request)
    {
        DB::beginTransaction();

        try {
            $bearer = $request->bearerToken();

            if (!$bearer) {
                return response()->json([
                    'success' => false,
                    'message' => 'Token Missing'
                ], 401);
            }

            PersonalAccessToken::where(
                'token',
                hash('sha256', $bearer)
            )->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Logout successful'
            ], 200);

        } catch (\Throwable $e) {

            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Server Error',
                'error'   => $e->getMessage()
            ], 500);
        }
    }
}
