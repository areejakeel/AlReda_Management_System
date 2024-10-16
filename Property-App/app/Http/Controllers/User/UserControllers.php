<?php

namespace App\Http\Controllers\User;

use App\Traits\ReturnResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserControllers extends AuthController
{
    use ReturnResponse;

    public function updateUser(Request $request): JsonResponse
    {
        $input = $request->all();
        $validator = Validator::make($input, [
            'name' => 'string',
            'email' => 'email',
        ]);
        if ($validator->fails()) {
            return $this->returnError(401, $validator->errors());
        }
        $user = Auth::user();
        if (isset( $input['name']))
        $user['name'] = $input['name'];
        if (isset( $input['email']))
            $user['email'] = $input['email'];
        $user->save();
        return $this->returnSuccessMessage("Update  Successfully");
    }

    //Change Password
    public  function changePassword(Request $request): JsonResponse
    {
        $reset=$request->all();
        $validator = Validator::make($reset, [
            'old_password'=>'required|min:8',
            'password' => 'required|min:8',
            'c_password' => 'required|same:password'
        ]);
        if ($validator->fails()) {
            return $this->returnError(401, $validator->errors());
        }
        $user=Auth::user();
        if (!Hash::check($reset['old_password'],$user['password'])){
            return $this->returnError(401,'The password is incorrect');
        }
        $user['password']=Hash::make($reset['password']);
        $user->save();
        return $this->returnSuccessMessage('Successfully');
    }
    public function me(): JsonResponse
    {
        return $this->returnData("user", Auth::user());
    }
}
