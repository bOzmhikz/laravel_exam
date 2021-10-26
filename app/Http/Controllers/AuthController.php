<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
  
    public function register(Request $request) {
        
        $fields = $request->validate([
            'email' => 'required|string',
            'password' =>'required|string'
        ]);
      
        $users = User::where('email', '=', $fields['email'])->first();
        if ($users === null) {
            $user = User::create([
                'email' => $fields['email'],
                'password' => bcrypt($fields['password'])
            ]);
    
            $token = $user->createToken('myapptoken')->plainTextToken;
    
            $response = [
                'message' => 'User Successfully Registered'
            ];
            
            return response($response, 201);
        } else {
            $response = [
                'message' => 'Email already taken'
            ];
            
            return response($response, 400);
        }       
    }

    public function login(Request $request) {
        
        $fields = $request->validate([
            'email' => 'required|string',
            'password' =>'required|string'
        ]);
      
        //Check Email
        $user = User::where('email', '=', $fields['email'])->first();

        //Check password
        if (!$user || !Hash::check($fields['password'], $user->password)) {

            // $attempts = $user->attempts -= 1;
            // User::where("email", $request->input('email'))->update(["attempts" => $attempts]);

            $response = [
                'message' => '“Invalid credentials'
            ];
            
            return response($response, 401);
            
        } else {
           
            $token = $user->createToken('myapptoken')->plainTextToken;

            $attempts = 5;
            User::where("email", $request->input('email'))->update(["attempts" => $attempts]);
    
            $response = [
                'access_token' => $token
            ];
            
            return response($response, 201);
        }       
    }

    public function logout( Request $requrest ) {

        auth()->user()->tokens()->delete();

        return [
            'message' => 'Logged out'
        ];

    }

}

