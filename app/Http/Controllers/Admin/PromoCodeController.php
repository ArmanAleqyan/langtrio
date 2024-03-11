<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Validator;
use App\Models\PromoCode;
class PromoCodeController extends Controller
{

    /**
     * @OA\Post(
     *     path="/api/admin/create_promo_code",
     *     summary="Create a new promo code",
     *     description="Creates a new promo code with the specified details.",
     *     tags={"Admin Promo Codes"},
     *     security={
     *         {"passport": {}}
     *     },
     *     @OA\RequestBody(
     *         required=true,
     *         description="Promo code details",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(property="agent_id", type="integer", description="ID of the agent associated with the promo code"),
     *                 @OA\Property(property="code", type="string", description="Promo code (min: 5 characters)"),
     *                 @OA\Property(property="end_date", type="string", format="date", description="End date of the promo code (format: YYYY-MM-DD)"),
     *                 @OA\Property(property="job_count", type="integer", description="Number of jobs for the promo code to be valid"),
     *                 @OA\Property(property="discount", type="integer", description="Discount amount for the promo code"),
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Promo code created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", description="Promo code created successfully"),
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
    public function create_promo_code(Request $request){
        $rules=array(
            'agent_id' => 'required|exists:agents,id',
            'code' => 'required|min:5|unique:promo_codes,code',
            'end_date' => 'required|date|after:now',
            'job_count' => 'required|integer',
            'discount' => 'required|integer',
        );
        $validator=Validator::make($request->all(),$rules);
        if($validator->fails())
        {
            return response()->json([
                'status' => false,
                'message' =>$validator->errors()
            ],400);
        }
        PromoCode::create([
           'agent_id' => $request->agent_id,
           'code' => $request->code,
           'job_count' => $request->job_count,
           'discount' => $request->discount,
           'end_date' => Carbon::parse($request->end_date),
        ]);


        return response()->json([
           'status' => true,
           'message' => 'Created'
        ],200);
    }

    /**
     * @OA\Get(
     *     path="/api/admin/single_page_promo_code",
     *     summary="Get information of a single promo code",
     *     description="Retrieves information of a single promo code based on the provided code_id.",
     *     tags={"Admin Promo Codes"},
     *     security={
     *         {"passport": {}}
     *     },
     *     @OA\Parameter(
     *         name="code_id",
     *         in="query",
     *         required=true,
     *         description="ID of the promo code to retrieve",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Promo code information retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Promo code not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", description="Code Not found"),
     *         )
     *     ),
     * )
     */
    public function single_page_promo_code(Request $request){
        $get = PromoCode::where('id', $request->code_id)->with('agent')->first();
        if ($get == null){
            return response()->json([
               'status' => false,
               'message' => 'Code Not found'
            ],404);
        }
        return response()->json([
           'status' => true,
           'data' => $get
        ],200);
    }

    /**
     * @OA\Post(
     *     path="/api/admin/update_promo_code",
     *     summary="Update promo code information",
     *     description="Updates the information of an existing promo code based on the provided code_id.",
     *     tags={"Admin Promo Codes"},
     *     security={
     *         {"passport": {}}
     *     },
     *     @OA\RequestBody(
     *         required=true,
     *         description="Promo code details to update",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(property="code_id", type="integer", description="ID of the promo code to update"),
     *                 @OA\Property(property="agent_id", type="integer", description="ID of the agent associated with the promo code"),
     *                 @OA\Property(property="code", type="string", description="Promo code (min: 5 characters)"),
     *                 @OA\Property(property="end_date", type="string", format="date", description="End date of the promo code (format: YYYY-MM-DD)"),
     *                 @OA\Property(property="job_count", type="integer", description="Number of jobs for the promo code to be valid"),
     *                 @OA\Property(property="discount", type="integer", description="Discount amount for the promo code"),
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Promo code information updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", description="Promo code updated successfully"),
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
    public function update_promo_code(Request $request){
        $rules=array(
            'code_id' => 'required|exists:promo_codes,id',
            'agent_id' => 'required|exists:agents,id',
            'code' => 'required|min:5|unique:promo_codes,code',
            'end_date' => 'required|date|after:now',
            'job_count' => 'required|integer',
            'discount' => 'required|integer',
        );
        $validator=Validator::make($request->all(),$rules);
        if($validator->fails())
        {
            return response()->json([
                'status' => false,
                'message' =>$validator->errors()
            ],400);
        }

        PromoCode::where('id', $request->code_id)->update([
            'agent_id' => $request->agent_id,
            'code' => $request->code,
            'job_count' => $request->job_count,
            'discount' => $request->discount,
            'end_date' => Carbon::parse($request->end_date),
        ]);
    }



    /**
     * @OA\Get(
     *     path="/api/admin/get_all_promo_codes",
     *     summary="Get all promo codes with optional search",
     *     description="Retrieves a paginated list of all promo codes with optional search functionality.",
     *     tags={"Admin Promo Codes"},
     *     security={
     *         {"passport": {}}
     *     },
     *     @OA\Parameter(
     *         name="search",
     *         in="query",
     *         description="Search keyword to filter promo codes by code",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Promo codes retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *         )
     *     ),
     * )
     */
    public function get_all_promo_codes(Request $request){
        $get = PromoCode::query();


        if (isset($request->search)){
            $keyword =$request->search;
            $name_parts = explode(" ", $keyword);
            foreach ($name_parts as $part) {
                $get->orWhere(function ($query) use ($part) {
                    $query->where('code', 'like', "%{$part}%")
                    ;
                });
            }
        }
        $get =  $get->simplepaginate(10);
        return response()->json([
           'status' => true,
           'data' => $get
        ],200);

    }
}
