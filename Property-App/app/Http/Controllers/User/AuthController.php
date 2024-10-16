<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Traits\ReturnResponse;

class AuthController extends Controller
{
    use ReturnResponse;
    public function login(): JsonResponse
    {
        $credentials = request(['email', 'password']);
        $token = auth()->attempt($credentials);
        if (!$token) {
            return $this->returnError(401, 'Unauthorized');
        }
        $user = auth()->user();
        $user['token'] = $token;
        return $this->returnData('user', $user, 'User login successfully');
    }

    /**
     * register
     *
     */
    public function register(Request $request): JsonResponse
    {
        $input = $request->all();
        $validator = Validator::make($input, [
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8',
            'c_password' => 'required|same:password'
        ]);
        if ($validator->fails()) {
            return $this->returnError(401, $validator->errors());
        }

        $input['password'] = Hash::make($input['password']);
        $user = User::create($input);
        $user->roles()->syncWithoutDetaching([1]);
        $user['token'] = auth()->login($user);
        return $this->returnData('user', $user, 'User registered sand your email ');
    }
    public function logout(): JsonResponse
    {
        auth()->logout();

        return $this->returnSuccessMessage('Successfully logged out');
    }


}
