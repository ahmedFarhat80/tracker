<?php

namespace App\Http\Controllers\Api\Auth\Restaurant;
use App\Http\Controllers\Controller;
use App\Http\Resources\Auth\Restaurant\LoginResource;
use App\Http\Requests\Auth\Restaurant\LoginRequest;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Http\Request;
use App\Models\Restaurant;
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

            if (!$token = auth('restaurant-api')->attempt(['email' => $email, 'password' => $password, 'status'=>1])) {
                return errorMessage('Unauthenticated', 403);
            }

            $restaurant = auth('restaurant-api')->user();
            $restaurant->token = $token;
            return new LoginResource($restaurant, $token);

        } catch (JWTException $ex) {
            return errorMessage($ex->getMessage(), 500);
        }

    }

    // ========================== LOGOUT ===================================

    public function logout (Request $request) {

        // JWTAuth::invalidate(JWTAuth::getToken()); // LOGOUT
        
        auth('restaurant-api')->logout();

        $response = ['message' => 'You have been successfully logged out!'];
        return response($response, 200);
    }
}
