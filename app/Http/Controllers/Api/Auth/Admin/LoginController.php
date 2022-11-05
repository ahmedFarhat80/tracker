<?php

namespace App\Http\Controllers\Api\Auth\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\Auth\Admin\LoginResource;
use App\Http\Requests\Auth\Admin\LoginRequest;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Http\Request;
use App\Models\Admin;
use JWTAuth;
use Auth;

class LoginController extends Controller
{
    // ========================== LOGIN ===================================

    public function login(LoginRequest $request)
    {
        // $credentials = $request->only('email', 'password');

        try {
            $email      = $request->email;
            $password   = $request->password;

            if (!$token = auth('admin-api')->attempt(['email' => $email, 'password' => $password, 'status'=>1])) {
                return errorMessage('Unauthenticated', 403);
            }

            $admin = auth('admin-api')->user();
            $admin->token = $token;

            return new LoginResource($admin, $token);

        } catch (JWTException $ex) {
            return errorMessage($ex->getMessage(), 500);
        }
    }

    // ========================== LOGOUT ===================================

    public function logout (Request $request) {
        // JWTAuth::invalidate(JWTAuth::getToken()); // LOGOUT

        auth('web')->logout();
        
        $response = ['message' => 'You have been successfully logged out!'];
        return response($response, 200);
    }

}
