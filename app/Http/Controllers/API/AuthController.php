<?php
namespace App\Http\Controllers\API;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use app\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(),
        [
            'username' => 'required',
            'email' => 'required|max:191',
            'password' => 'required',
        ]);

        if($validator->fails())
        {
            return response()->json([
                'validation_errors'->$validator->messages(),
            ]);
        }
        else{
            $user = User::create([
                'username' =>$request->username,
                'email' =>$request->email,
                'password' =>Hash::make($request->password),
            ]);

            $token = $user->createToken($user->email.'_Token')->plainTextToken;

            return response()->json([
                'status'=>200,
                'username'=>$user->username,
                'token'=>$token,
                'message'=>'Registered Successfully',
            ]);
        }
    }
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(),
        [
            'email' => 'required|max:191',
            'password' => 'required',
        ]);

        if($validator->fails())
        {
            return response()->json([
                'validation_errors'->$validator->messages(),
            ]);
        }
        else
        {
            $user = User::where('email', $request->email)->first();
 
            if (! $user || ! Hash::check($request->password, $user->password)) 
            {
                return response()->json([
                    'status' => 401,
                    'message'=>'Invalid Credentials',
                ]);
            }
            else
            {
               $token = $user->createToken($user->email.'_Token')->plainTextToken;

                return response()->json([
                    'status'=>200,
                    'email'=>$user->email,
                    'token'=>$token,
                    'message'=>'Logged In Successfully',
                ]);
            }
        }
    }
    public function logout()
    {
        auth()->user()->tokens()->delete();
        return response()->json([
            'status'=>200,
            'message'=>'Logged Out Successfully'
        ]);
    }



}