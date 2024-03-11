<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Validator;
class ModeratorController extends Controller
{


    /**
     * @OA\Post(
     *     path="/api/admin/create_moderator",
     *     summary="Create a new moderator",
     *     description="Creates a new moderator user with the specified email, name, and password.",
     *     tags={"Admin Moderators"},
     *     security={
     *         {"passport": {}}
     *     },
     *     @OA\RequestBody(
     *         required=true,
     *         description="Moderator details",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(property="email", type="string", format="email", description="Moderator's email"),
     *                 @OA\Property(property="name", type="string", description="Moderator's name"),
     *                 @OA\Property(property="password", type="string", description="Moderator's password (min: 8 characters)"),
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Moderator created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", description="Moderator created successfully"),
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
     * )
     */
    public function create_moderator(Request $request){
        $rules=array(
            'email' => 'required|email|unique:users,email',
            'name' => 'required',
            'password' => 'required|min:8|max:100'
        );
        $validator=Validator::make($request->all(),$rules);
        if($validator->fails())
        {
            return response()->json([
                'status' => false,
                'message' =>$validator->errors()
            ],400);
        }


        User::create([
           'email' => $request->email,
           'password' => Hash::make($request->password),
           'name' => $request->name,
           'role_id' => 2
        ]);
        return response()->json([
           'status' => true,
           'message' =>  'created'
        ],200);
    }

    /**
     * @OA\Post(
     *     path="/api/admin/update_moderator",
     *     summary="Update moderator information",
     *     description="Updates the information of an existing moderator user.",
     *     tags={"Admin Moderators"},
     *     security={
     *         {"passport": {}}
     *     },
     *     @OA\RequestBody(
     *         required=true,
     *         description="Moderator details to update",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(property="user_id", type="integer", description="ID of the moderator user to update"),
     *                 @OA\Property(property="email", type="string", format="email", description="New email for the moderator"),
     *                 @OA\Property(property="name", type="string", description="New name for the moderator"),
     *                 @OA\Property(property="password", type="string", description="New password for the moderator (min: 8 characters)"),
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Moderator updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", description="Moderator updated successfully"),
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
     * )
     */

    public function update_moderator(Request $request){
        $rules=array(
            'user_id' => 'required|exists:users,id',
            'email' => 'required|email|unique:users,email,' . $request->user_id,
            'name' => 'required',
            'password' => 'min:8|max:100'
        );
        $validator=Validator::make($request->all(),$rules);
        if($validator->fails())
        {
            return response()->json([
                'status' => false,
                'message' =>$validator->errors()
            ],400);
        }


        User::where('id', $request->user_id)->update([
           'email' => $request->email,
           'name' => $request->name,
        ]);


        if (isset($request->password)){
            User::where('id', $request->user_id)->update([
               'password' => Hash::make($request->password)
            ]);
        }



        return response()->json([
           'status' => True,
            'message' => 'Updated'
        ],200);

    }
    /**
     * @OA\Get(
     *     path="/api/admin/single_page_moderator",
     *     summary="Get information of a single moderator",
     *     description="Retrieves information of a single moderator user based on the provided moderator_id.",
     *     tags={"Admin Moderators"},
     *     security={
     *         {"passport": {}}
     *     },
     *     @OA\Parameter(
     *         name="moderator_id",
     *         in="query",
     *         required=true,
     *         description="ID of the moderator user to retrieve",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Moderator information retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Moderator not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", description="Not Found Moderator"),
     *         )
     *     ),
     * )
     */
    public function single_page_moderator(Request $request){

        $get = User::where('id', $request->moderator_id)->first();

        if ($get == null){
            return response()->json([
               'status' => false,
               'message' => 'Not Found Moderator'
            ],404);
        }

        return response()->json([
           'status' => true,
           'data' => $get
        ],200);
    }
    /**
     * @OA\Get(
     *     path="/api/admin/get_all_moderators",
     *     summary="Get all moderators",
     *     description="Retrieves a paginated list of all moderators.",
     *     tags={"Admin Moderators"},
     *     security={
     *         {"passport": {}}
     *     },
     *     @OA\Response(
     *         response=200,
     *         description="Moderators retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *         )
     *     ),
     * )
     */

    public function get_all_moderators(){
        $get = User::where('role_id', 2)->simplepaginate(10);


        return response()->json([
           'status' => true,
           'data' => $get
        ],200);
    }






}
