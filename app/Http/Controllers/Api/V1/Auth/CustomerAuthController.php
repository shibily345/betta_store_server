<?php

namespace App\Http\Controllers\Api\V1\Auth;
use App\CentralLogics\Helpers;

use App\Http\Controllers\Controller;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;


class CustomerAuthController extends Controller
{
    
     public function login(Request $request)
    {
          $validator = Validator::make($request->all(), [
            'phone' => 'required',
            'password' => 'required|min:6'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }
        $data = [
            'phone' => $request->phone,
            'password' => $request->password
        ];
        
        if (auth()->attempt($data)) {
            //auth()->user() is coming from laravel auth:api middleware
            $token = auth()->user()->createToken('RestaurantCustomerAuth')->accessToken;
            if(!auth()->user()->status)
            {
                $errors = [];
                array_push($errors, ['code' => 'auth-003', 'message' => trans('messages.your_account_is_blocked')]);
                return response()->json([
                    'errors' => $errors
                ], 403);
            }
          
            return response()->json(['token' => $token, 'is_phone_verified'=>auth()->user()->is_phone_verified], 200);
        } else {
            $errors = [];
            array_push($errors, ['code' => 'auth-001', 'message' => 'Unauthorized.']);
            return response()->json([
                'errors' => $errors
            ], 401);
        }
    }
    
    public function register(Request $request)
    {  
        $validator = Validator::make($request->all(), [
            'f_name' => 'required|unique:users,f_name', // Check if 'f_name' is unique in the 'users' table
            //'l_name' => 'required',
            'email' => 'required|unique:users',
            'phone' => 'required|unique:users',
            'password' => 'required|min:6',
        ], [
            'f_name.required' => 'The first name field is required.',
            'f_name.unique' => 'The name is already taken. Please choose a different one.',
            'phone.required' => 'The phone field is required.',
        ]);
    
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 403);
        }
        
      
       
        $user = User::create([
            'f_name' => $request->f_name,
            //'l_name' => $request->l_name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => bcrypt($request->password),
        ]);

        $token = $user->createToken('RestaurantCustomerAuth')->accessToken;

       
        return response()->json(['token' => $token,'is_phone_verified' => 0, 'phone_verify_end_url'=>"api/v1/auth/verify-phone" ], 200);
    }
    
    public function update(Request $request, $id)
   {
    $user = User::find($id); // Find the user by ID

    if (!$user) {
        return response()->json(['message' => 'User not found'], 404);
    }

    $user->update($request->all()); // Update user data with the request data

    return response()->json(['message' => 'User data updated successfully'], 200);
   }

    public function index()
    {
        $users = User::all(); // Fetch all users from the database
        return response()->json(['users' => $users], 200);
    }
    public function changePasswordByPhoneNumber(Request $request) { 
        // Validate the request data
        $request->validate([
            'phone' => 'required|string',  
            'new_password' => 'required|string|min:6',
        ]);
    
        // Find the user by phone number
        $user = User::where('phone', $request->phone)->first();
    
        // Check if the user exists
        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }
    
        // Hash the new password
        $newPassword = bcrypt($request->new_password);
    
        // Update the user's password
        $user->update([
            'password' => $newPassword,
        ]);
        
        return response()->json(['message' => 'Password changed successfully'], 200);
    }
}
