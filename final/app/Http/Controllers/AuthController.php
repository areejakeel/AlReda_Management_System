<?php

namespace App\Http\Controllers;
use App\Models\User;
use App\Models\Admin;
use App\Models\Role;
use App\Models\patient;
use App\Models\Wallet;
use Tymon\JWTAuth\Contracts\JWTSubject;
use JWTAuth;
use Laravel\Sanctum\PersonalAccessToken;
use Illuminate\Support\Facades\DB;
//use app\Traits\ReturnResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Auth;



class AuthController extends Controller
{

    protected Wallet $Wallet;

    public function __construct(Wallet $wallet){
        $this->Wallet = $wallet;
    }


    public function patientregister(Request $request): JsonResponse
    {
        $input = $request->all();

        $validator = Validator::make($input, [
            'first_name' => 'required',
            'father_name' => 'required',
            'last_name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8',
            'phone' => 'required|regex:/^09[0-9]{8}$/'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson(), 400);
        }
        $user = new User();
        $user->first_name = $request->input('first_name');
        $user->last_name = $request->input('last_name');
        $user->email = $request->input('email');
        $user->password = Hash::make($request->input('password'));
        $user->phone = $request->input('phone');
        $user->role_id = 4;
        $user->save();
        ################################## Wallet Creation #################################
        $this->Wallet->store($user->id,$user->email);
        ####################################################################################
        $existingPatient = Patient::where('first_name', $request->input('first_name'))
                                  ->where('father_name', $request->input('father_name'))
                                  ->where('last_name', $request->input('last_name'))
                                  ->first();
        if ($existingPatient) {
            $existingPatient->user_id = $user->id;
            $existingPatient->save();

            return response()->json([
                'message' => 'User successfully registered and linked to existing patient',
                'user' => $user,
                'patient' => $existingPatient
            ], 201);
        }
        $patient = new Patient();
        $patient->first_name = $request->input('first_name');
        $patient->father_name = $request->input('father_name');
        $patient->last_name = $request->input('last_name');
        $patient->user_id = $user->id;
        $patient->save();
        return response()->json([
            'message' => 'Patient successfully registered',
            'user' => $user,
            'patient' => $patient
        ], 201);
    }


    public function Adminregister(Request $request): JsonResponse
    {
        $input = $request->all();
        $validator = Validator::make($input, [
            'first_name' => 'required',
            'last_name'=> 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8',
            'phone'=>'required',
        ]);
    if ($validator->fails()) {
    return response()->json($validator->errors()->toJson(), 400);
}

// Create a new user instance
$user = new User();
$user->first_name = $request->input('first_name');
$user->last_name = $request->input('last_name');
$user->email = $request->input('email');
$user->password = Hash::make($request->input('password')); // Encrypt password
$user->phone = $request->input('phone');
$user->role_id = 1; // Assign role ID for admin
$user->save();

// Create a new admin instance
$admin = new Admin();
$admin->first_name = $request->input('first_name');
$admin->last_name = $request->input('last_name');
$admin->phone = $request->input('phone');
$admin->email = $request->input('email');
$admin->password = Hash::make($request->input('password')); // Encrypt password
$admin->save();

// Return response
return response()->json([
    'message' => 'Admin successfully registered',
    'admin' => $admin,
], 201);}



public function login(Request $request)
{
    // Validate user input
    $credentials = $request->only('email', 'password');

    try {
        if (!$token = JWTAuth::attempt($credentials)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }
    } catch (JWTException $e) {
        return response()->json(['message' => 'Could not create token'], 500);
    }
    $user = Auth::user();
    $role = '';
    switch ($user->role_id) {
        case 1:
            $role = 'admin';
            $id=1;
            break;
        case 2:
            $role = 'doctor';
            $user_id = auth()->user()->id;
            $id = DB::table('doctors')
            ->where('user_id', $user_id)
            ->value('id');
            break;
            case 3:
            $user_id = auth()->user()->id;
            $id = DB::table('secretary')
            ->where('user_id', $user_id)
            ->value('id');
            $role = 'secretary';
            break;
       case 4:
          $role='user';
            $user_id = auth()->user()->id;
            $id = DB::table('patients')
            ->where('user_id', $user_id)
            ->value('id');
          break;

        default:
            $role = 'unknown';
            break;
    }

    // Return a JSON response with token, user details, and role
    return response()->json([
        'token' => $token,
        'user' => $user,
        'id'=>  $id,
        'role' => $role,
    ]);
}

public function deleteAccount(Request $request): JsonResponse
{
    $user = Auth::user();

    if ($user) {
        Patient::where('user_id', $user->id)->delete();
        $user->delete();

        return response()->json([
            'message' => 'Account successfully deleted',
        ], 200);
    }

    return response()->json(['error' => 'User not found'], 404);
}
public function logout(Request $request)
{
    try {
        $token = JWTAuth::parseToken()->getToken();

        if (!$token) {
            return response()->json(['error' => 'Token not provided'], 400);
        }

        JWTAuth::invalidate($token);

        return response()->json(['message' => 'Successfully logged out']);
    } catch (JWTException $e) {
        return response()->json(['error' => 'Failed to logout'], 500);
    }
}}

