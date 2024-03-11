<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TextController as TextModel;
use App\Models\Words;
use Validator;
class TextController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/add_texts",
     *     summary="Add texts",
     *     description="Add texts with optional photo and audio",
     *     operationId="addTexts",
     *     tags={"Admin Texts"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="photo", type="string", format="binary", description="Image file (jpeg, png, jpg, gif, webp)"),
     *             @OA\Property(property="audio_ru", type="string", format="binary", description="Audio file in Russian (mpeg, wav, mp3)"),
     *             @OA\Property(property="audio_en", type="string", format="binary", description="Audio file in English (mpeg, wav, mp3)"),
     *             @OA\Property(property="audio_fr", type="string", format="binary", description="Audio file in French (mpeg, wav, mp3)"),
     *             @OA\Property(property="category_id", type="integer", description="Category ID (required)"),
     *             @OA\Property(property="levels_id", type="integer", description="Levels ID (required)"),
     *             @OA\Property(property="text_ru", type="string", description="Text in Russian (required)"),
     *             @OA\Property(property="text_en", type="string", description="Text in English (required)"),
     *             @OA\Property(property="text_fr", type="string", description="Text in French (required)"),
     *              @OA\Property(property="title_ru", type="string", description="Title in Russian"),
     *             @OA\Property(property="title_en", type="string", description="Title in English"),
     *             @OA\Property(property="title_fr", type="string", description="Title in French"),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Text Created",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Text Created"),
     *             @OA\Property(property="text_id", type="integer", example=1),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad Request",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *         ),
     *     ),
     *     security={
     *         {"bearerAuth": {}}
     *     }
     * )
     */
    public function add_texts(Request  $request){
        $rules=array(
            'photo' => 'image|mimes:jpeg,png,jpg,gif,webp',
            'audio_ru' => 'mimes:audio/mpeg,audio/wav,audio/mp3',
            'audio_en' => 'mimes:audio/mpeg,audio/wav,audio/mp3',
            'audio_fr' => 'mimes:audio/mpeg,audio/wav,audio/mp3',
            'category_id' => 'required|exists:categories,id',
            'levels_id' => 'required|exists:levels,id',
            'text_ru' => 'required',
            'text_en' => 'required',
            'text_fr' => 'required',
            'title_fr' => 'required',
            'title_ru' => 'required',
            'title_en' => 'required',
        );
        $validator=Validator::make($request->all(),$rules);
        if($validator->fails())
        {
            return response()->json([
                'status' => false,
                'message' =>$validator->errors()
            ],400);
        }
        if (isset($request->photo)){
            $one_photo =  $request->photo;
            $fileName = time().'.'.$one_photo->getClientOriginalExtension();
            $filePath = $one_photo->move('uploads', $fileName);
        }
        if (isset($request->audio_ru)){
            $audio_ru =  $request->audio_ru;
            $audi_ru_name = time().time().'.'.$audio_ru->getClientOriginalExtension();
            $filePath = $audio_ru->move('uploads', $audi_ru_name);
        }
        if (isset($request->audio_en)){
            $audio_en =  $request->audio_en;
            $audi_en_name = time().time().time().'.'.$audio_en->getClientOriginalExtension();
            $filePath = $audio_en->move('uploads', $audi_en_name);
        }

        if (isset($request->audio_fr)){
            $audio_fr  =  $request->audio_fr;
            $audi_fr_name = time().time().time().time().'.'.$audio_fr->getClientOriginalExtension();
            $filePath = $audio_fr->move('uploads', $audi_fr_name);
        }
       $text =  TextModel::create([
           'user_id' => auth()->user()->id,
           'category_id' => $request->category_id,
           'levels_id' => $request->levels_id ,
           'title_ru' => $request->title_ru ,
           'title_en' => $request->title_en ,
           'title_fr' => $request->title_fr ,
           'text_ru' => $request->text_ru ,
           'text_en' => $request->text_en ,
           'text_fr' => $request->text_fr ,
           'audio_ru' => $audi_ru_name??null,
           'audio_en' => $audi_en_name??null ,
           'audio_fr' => $audi_fr_name??null,
           'photo' => $fileName??null,
        ]);


        return  response()->json([
           'status' => true,
           'message' => 'Text Created',
            'text_id' => $text->id
        ]);
    }


    /**
     * @OA\Get(
     *     path="/api/get_all_texts",
     *     summary="Get all texts",
     *     description="Retrieve all texts with optional filters",
     *     operationId="getAllTexts",
     *     tags={"Admin Texts"},
     *     @OA\Parameter(
     *         name="user_id",
     *         in="query",
     *         description="User ID to filter texts by",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="category_id",
     *         in="query",
     *         description="Category ID to filter texts by",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="level_id",
     *         in="query",
     *         description="Level ID to filter texts by",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="search",
     *         in="query",
     *         description="Search keyword to filter texts by title in multiple languages",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
 *         ),
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Bad Request",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Invalid input"),
 *         ),
 *     ),
 *     security={
    *         {"bearerAuth": {}}
    *     }
 * )
 */
    public function get_all_texts(Request  $request){
        $get = TextModel::query();


        if (isset($request->user_id)){
            $get->where('user_id', $request->user_id);
        }
        if (isset($request->category_id)){
            $get->where('category_id', $request->category_id);
        }

        if (isset($request->level_id)){
            $get->where('level_id', $request->level_id);
        }


        if(isset($request->search)){
            $keyword =$request->search;
            $name_parts = explode(" ", $keyword);
            foreach ($name_parts as $part) {
                $get->orWhere(function ($query) use ($part) {
                    $query->where('title_ru', 'like', "%{$part}%")
                        ->orwhere('title_en', 'like', "%{$part}%")
                        ->orwhere('title_fr', 'like', "%{$part}%")
                    ;
                });
            }
        }

        $get = $get->paginate(15);



        return response()->json([
           'status' => true,
           'data' => $get
        ],200);

    }

    /**
     * @OA\Get(
     *     path="/api/single_page_text",
     *     summary="Get single text",
     *     description="Retrieve details of a single text by its ID",
     *     operationId="getSingleText",
     *     tags={"Admin Texts"},
     *     @OA\Parameter(
     *         name="text_id",
     *         in="query",
     *         description="ID of the text to retrieve",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
*         ),
*     ),
*     @OA\Response(
 *         response=404,
 *         description="Not Found",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Not Found"),
 *         ),
 *     ),
 *     security={
    *         {"bearerAuth": {}}
    *     }
 * )
 */
    public function single_page_text(Request  $request){
        $get = TextModel::where('id', $request->text_id)->first();

        if ($get == null){
            return response()->json([
               'status' => false,
               'message' => 'Not Found'
            ],404);
        }


        $get_words = Words::where('text_id', $request->text_id)->get();

        return  response()->json([
           'status' => true,
           'data'  => $get,
            'words' => $get_words
        ],200);
    }

    /**
     * @OA\Post(
     *     path="/api/update_text",
     *     summary="Update text",
     *     description="Update details of a text by its ID",
     *     operationId="updateText",
     *     tags={"Admin Texts"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="photo", type="string", format="binary", description="Updated image file (jpeg, png, jpg, gif, webp)"),
     *             @OA\Property(property="audio", type="string", format="binary", description="Updated audio file (mpeg, wav, mp3)"),
     *             @OA\Property(property="text_id", type="integer", description="ID of the text to update (required)"),
     *             @OA\Property(property="category_id", type="integer", description="Updated category ID (required)"),
     *             @OA\Property(property="levels_id", type="integer", description="Updated levels ID (required)"),
     *             @OA\Property(property="text_ru", type="string", description="Updated text in Russian (required)"),
     *             @OA\Property(property="text_en", type="string", description="Updated text in English (required)"),
     *             @OA\Property(property="text_fr", type="string", description="Updated text in French (required)"),
     *             @OA\Property(property="title_ru", type="string", description="Updated title in Russian (required)"),
     *             @OA\Property(property="title_en", type="string", description="Updated title in English (required)"),
     *             @OA\Property(property="title_fr", type="string", description="Updated title in French (required)"),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Updated",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Updated"),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad Request",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false)
     *         ),
     *     ),
     *     security={
     *         {"bearerAuth": {}}
     *     }
     * )
     */

    public function update_text(Request  $request){
        $rules=array(
            'photo' => 'image|mimes:jpeg,png,jpg,gif,webp',
            'audio_ru' => 'mimes:audio/mpeg,audio/wav,audio/mp3',
            'audio_en' => 'mimes:audio/mpeg,audio/wav,audio/mp3',
            'audio_fr' => 'mimes:audio/mpeg,audio/wav,audio/mp3',
            'text_id' => 'required|exists:texts,id',
            'category_id' => 'required|exists:categories,id',
            'levels_id' => 'required|exists:levels,id',
            'text_ru' => 'required',
            'text_en' => 'required',
            'text_fr' => 'required',
            'title_fr' => 'required',
            'title_ru' => 'required',
            'title_en' => 'required',
        );
        $validator=Validator::make($request->all(),$rules);
        if($validator->fails())
        {
            return response()->json([
                'status' => false,
                'message' =>$validator->errors()
            ],400);
        }

        $get = TextModel::where('id', $request-->text_id)->first();



        $get->update([
            'user_id' => auth()->user()->id,
            'category_id' => $request->category_id,
            'levels_id' => $request->levels_id ,
            'title_ru' => $request->title_ru ,
            'title_en' => $request->title_en ,
            'title_fr' => $request->title_fr ,
            'text_ru' => $request->text_ru ,
            'text_en' => $request->text_en ,
            'text_fr' => $request->text_fr ,
            'audio_ru' => $audi_ru_name??$get->audio_ru,
            'audio_en' => $audi_en_name??$get->audio_en ,
            'audio_fr' => $audi_fr_name??$get->audio_fr,
            'photo' => $fileName??$get->photo,
        ]);


        return  response()->json([
            'status' => true,
            'message' => 'Updated'
        ],200);

    }


}
