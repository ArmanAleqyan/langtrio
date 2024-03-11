<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Level;
use Validator;
class LevelController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/admin/all_levels",
     *     summary="Get all levels",
     *     description="Retrieves a list of all levels.",
     *     tags={"Admin Levels"},
     *     @OA\Response(
     *         response=200,
     *         description="Levels retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *         )
     *     ),
     * )
     */
    public function all_levels(){
        $get = Level::get();


        return response()->json([
           'status' => true,
           'data' => $get
        ],200);
    }


    /**
     * @OA\Get(
     *     path="/api/admin/single_page_level",
     *     summary="Get information of a single level",
     *     description="Retrieves information of a single level based on the provided level_id.",
     *     tags={"Admin Levels"},
     *     @OA\Parameter(
     *         name="level_id",
     *         in="query",
     *         required=true,
     *         description="ID of the level to retrieve",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Level information retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Level not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", description="Level not found"),
     *         )
     *     ),
     * )
     */
    public function single_page_level(Request $request){
        $get = Level::where('id', $request->level_id)->first();


        if ($get == null){
            return response()->json([
               'status' => false,
               'message' => 'Not Found this level_id'
            ],404);
        }

        return response()->json([
           'status' => true,
           'data' => $get
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/admin/update_level",
     *     summary="Update level details",
     *     description="Updates the details of a level based on the provided level_id.",
     *     tags={"Admin Levels"},
     *     @OA\Parameter(
     *         name="level_id",
     *         in="query",
     *         required=true,
     *         description="ID of the level to update",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         description="Updated level details",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(property="name", type="string", description="Updated name of the level"),
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Level details updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", description="Level details updated successfully"),
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *         )
     *     ),
     * )
     */
    public function update_level(Request $request){

        $rules=array(
            'level_id' => 'required|exists:levels,id',
            'name' => 'required',
        );
        $validator = Validator::make($request->all(),$rules);
        if($validator->fails())
        {
            return response()->json([
                'status' => false,
                'message' =>$validator->errors()
            ],400);
        }

        $get  = Level::where('id', $request->level_id)->update([
           'name' => $request->name
        ]);



        return response()->json([
           'status' => true,
           'message' => 'Updated'
        ],200);

    }
    /**
     * @OA\Post(
     *     path="/api/admin/create_level",
     *     summary="Create a new level",
     *     description="Creates a new level with the provided details.",
     *     tags={"Admin Levels"},
     *     @OA\RequestBody(
     *         required=true,
     *         description="Level details",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(property="name", type="string", description="Name of the new level"),
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Level created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", description="Level created successfully"),
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *         )
     *     ),
     * )
     */

    public function create_level(Request $request){
        $rules=array(
            'name' => 'required',
        );
        $validator = Validator::make($request->all(),$rules);
        if($validator->fails())
        {
            return response()->json([
                'status' => false,
                'message' =>$validator->errors()
            ],400);
        }

        Level::create([
           'name' => $request->name
        ]);



        return response()->json([
           'status' => true,
           'message' => 'Created'
        ],200);
    }
}
