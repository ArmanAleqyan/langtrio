<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use Validator;
class CategoryController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/admin/create_category",
     *     summary="Create a new category",
     *     description="Creates a new category with the provided details.",
     *     tags={"Admin Categories"},
     *     @OA\RequestBody(
     *         required=true,
     *         description="Category details",
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property(property="photo", type="file", description="Category photo", format="binary"),
     *                 @OA\Property(property="name_ru", type="string", description="Name of the category in Russian"),
     *                 @OA\Property(property="name_en", type="string", description="Name of the category in English"),
     *                 @OA\Property(property="name_fr", type="string", description="Name of the category in French"),
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Category created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", description="Category created successfully"),
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
    public function create_category(Request $request){

        $rules=array(
            'photo' => 'required|image|mimes:jpeg,png,jpg,gif,webp',
            'name_ru' => 'required',
            'name_en' => 'required',
            'name_fr' => 'required',
        );
        $validator=Validator::make($request->all(),$rules);
        if($validator->fails())
        {
            return response()->json([
                'status' => false,
                'message' =>$validator->errors()
            ],400);
        }
        $one_photo =  $request->photo;
        $fileName = time().'.'.$one_photo->getClientOriginalExtension();
        $filePath = $one_photo->move('uploads', $fileName);


        Category::create([
           'photo' => $fileName,
           'name_ru' => $request->name_ru,
           'name_en' => $request->name_en,
           'name_fr' => $request->name_fr,
        ]);


        return response()->json([
           'status' => true,
           'message' => 'Created'
        ],200);
    }
    /**
     * @OA\Get(
     *     path="/api/admin/all_category",
     *     summary="Get all categories",
     *     description="Retrieves a list of all categories.",
     *     tags={"Admin Categories"},
     *     @OA\Response(
     *         response=200,
     *         description="Categories retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *         )
     *     ),
     * )
     */
    public function all_category(){
        $get = Category::orderby('id', 'desc')->get();


        return response()->json([
           'status' => true,
           'data' => $get
        ],200);
    }
    /**
     * @OA\Get(
     *     path="/api/admin/single_page_category",
     *     summary="Get information of a single category",
     *     description="Retrieves information of a single category based on the provided category_id.",
     *     tags={"Admin Categories"},
     *     @OA\Parameter(
     *         name="category_id",
     *         in="query",
     *         required=true,
     *         description="ID of the category to retrieve",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Category information retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Category not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", description="Category not found"),
     *         )
     *     ),
     * )
     */
    public function single_page_category(Request $request){
            $get = Category::where('id', $request->category_id)->first();


            if ($get == null){
                return  response()->json([
                   'status' => false,
                   'message'  => '404 Not Found category_id'
                ],404);
            }

            return response()->json([
               'status' => true,
               'data' => $get
            ],200);
    }
    /**
     * @OA\Post(
     *     path="/api/admin/update_category",
     *     summary="Update a category",
     *     description="Updates an existing category with the provided details.",
     *     tags={"Admin Categories"},
     *     @OA\RequestBody(
     *         required=true,
     *         description="Category details",
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property(property="category_id", type="integer", description="ID of the category to update"),
     *                 @OA\Property(property="photo", type="file", description="Category photo", format="binary"),
     *                 @OA\Property(property="name_ru", type="string", description="Updated name of the category in Russian"),
     *                 @OA\Property(property="name_en", type="string", description="Updated name of the category in English"),
     *                 @OA\Property(property="name_fr", type="string", description="Updated name of the category in French"),
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Category updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", description="Category updated successfully"),
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
    public function update_category(Request $request){

        $rules=array(
            'category_id' => 'required|exists:categories,id',
            'photo' => 'image|mimes:jpeg,png,jpg,gif,webp',
            'name_ru' => 'required',
            'name_en' => 'required',
            'name_fr' => 'required',
        );
        $validator=Validator::make($request->all(),$rules);
        if($validator->fails())
        {
            return response()->json([
                'status' => false,
                'message' =>$validator->errors()
            ],400);
        }

        $get = Category::where('id', $request->category_id)->first();


        if (isset($request->photo)){
            $one_photo =  $request->photo;
            $fileName = time().'.'.$one_photo->getClientOriginalExtension();
            $filePath = $one_photo->move('uploads', $fileName);
        }else{
            $fileName= $get->photo;
        }



        $get->update([
            'photo' => $fileName,
            'name_ru' => $request->name_ru,
            'name_en' => $request->name_en,
            'name_fr' => $request->name_fr,
        ]);

        return response()->json([
           'status' => true,
           'message' => 'updated'
        ],200);

    }
    /**
     * @OA\Delete(
     *     path="/api/admin/delete_category",
     *     summary="Delete a category",
     *     description="Deletes an existing category based on the provided category_id.",
     *     tags={"Admin Categories"},
     *     @OA\Parameter(
     *         name="category_id",
     *         in="query",
     *         required=true,
     *         description="ID of the category to delete",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Category deleted successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", description="Category deleted successfully"),
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
    public function delete_category(Request $request){

        $rules=array(
            'category_id' => 'required|exists:categories,id',

        );
        $validator=Validator::make($request->all(),$rules);
        if($validator->fails())
        {
            return response()->json([
                'status' => false,
                'message' =>$validator->errors()
            ],400);
        }


        $get = Category::where('id', $request->category_id)->first();

        $fileFullPath = public_path("uploads/$get->photo");
        $get->delete();
        if (file_exists($fileFullPath)) {
            unlink($fileFullPath);
        }

        return response()->json([
           'status' => true,
           'message' => 'Deleted'
        ],200);
    }
}
