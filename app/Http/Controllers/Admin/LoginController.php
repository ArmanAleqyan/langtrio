<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Validator;
class LoginController extends Controller
{

    /**
     * @OA\Post(
     *     path="/api/admin/login",
     *     summary="Admin Auth",
     *     description="Logs in a user and returns an access token.",
     *     tags={"Admin Authentication"},
     *     @OA\RequestBody(
     *         required=true,
     *         description="User credentials",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(property="email", type="string", format="email", description="User's email"),
     *                 @OA\Property(property="password", type="string", description="User's password"),
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful login",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="token", type="string", description="Access token"),
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="object", description="Validation errors"),
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Wrong email",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", description="Wrong email"),
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Wrong email or password",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", description="Wrong email or password"),
     *         )
     *     ),
     * )
     */

    public function login(Request $request){

        $rules=array(
            'email' => 'required|email',
            'password' => 'required'
        );
        $validator=Validator::make($request->all(),$rules);
        if($validator->fails())
        {
            return response()->json([
                'status' => false,
                'message' =>$validator->errors()
            ],400);
        }

        $get_user = User::where('email', $request->email)->with('role')->first();



        if ($get_user == null || $get_user->role_id != 1 && $get_user->role_id != 2){
            return response()->json([
                'status' => false,
                'message' => 'Wrong Email'
            ],401);
        }

        $data =[
            'email' => $request->email,
            'password' => $request->password
        ];


        $login =  Auth::attempt($data);

        if ($login == false){
            return response()->json([
                'status' => false,
                'message' => 'Wrong Email Or Password'
            ],422);
        }


        $token =  $get_user->createtoken('Laravel Passport Token')->accessToken;



        return response()->json([
            'status' => true,
            'user' => $get_user,
            'token' => $token
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/admin/logout",
     *     summary="Logout user",
     *     description="Logs out the authenticated user and revokes the access token.",
     *     tags={"Admin Authentication"},
     *     security={
     *         {"passport": {}}
     *     },
     *     @OA\Response(
     *         response=200,
     *         description="Successful logout",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", description="Logout successful"),
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", description="Unauthenticated"),
     *         )
     *     ),
     * )
     */
    public function logout(Request $request){

        $user = \auth()->user();
        $user->tokens()->delete();


        return response()->json([
            'status' => true,
            'message' => 'Logout ed'
        ],200);
    }
}
