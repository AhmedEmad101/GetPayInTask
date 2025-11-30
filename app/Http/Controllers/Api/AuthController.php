<?php

namespace App\Http\Controllers\Api;
use App\Actions\Auth\loginAction;
use App\Actions\Auth\logoutAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use Illuminate\Http\Request;
use App\Traits\ApiResponseTrait;
class AuthController extends Controller
{
    use ApiResponseTrait;
    public function login(LoginRequest $request, LoginAction $loginAction)
    {
        try {
            $data = $request->validated();
            $token = $loginAction->execute($data['email'], $data['password']);

            if (! $token) {
                return $this->errorResponse('Invalid credentials', 401);
            }

            return $this->successResponse([
                'token' => $token,
                'user' => auth()->user()
            ], 'Login successful!');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    public function logout(Request $request)
    {
       try {
            (new logoutAction)->execute($request);
            return $this->successResponse(null, 'Logged out successfully');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }
     public function login_execption(Request $request)
    {
        return $this->errorResponse('You have to login', 401);
    }
}
