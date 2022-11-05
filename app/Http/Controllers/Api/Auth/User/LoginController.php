<?php

namespace App\Http\Controllers\Api\Auth\User;

use App\Http\Controllers\Controller;
use App\Http\Resources\Auth\User\LoginResource;
use App\Http\Requests\Auth\User\LoginRequest;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Http\Request;
use App\Models\User;
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

            if (!$token = auth('user-api')->attempt(['email' => $email, 'password' => $password, 'status'=>1])) {
                return errorMessage('Unauthenticated', 403);
            }

            $user = auth('user-api')->user();
            $user->token = $token;

            return new LoginResource($user, $token);

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
