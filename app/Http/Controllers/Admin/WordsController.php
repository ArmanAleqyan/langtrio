<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Words;
use Validator;
class WordsController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/get_all_words",
     *     summary="Get all words",
     *     description="Get a list of words based on filters and search query",
     *     operationId="getAllWords",
     *     tags={"Admin Words"},
     *     @OA\Parameter(
     *         name="category_id",
     *         in="query",
     *         description="Filter words by category ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="level_id",
     *         in="query",
     *         description="Filter words by level ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="text_id",
     *         in="query",
     *         description="Filter words by text ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="search",
     *         in="query",
     *         description="Search query for words",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *         ),
     *     ),
     *     security={
     *         {"bearerAuth": {}}
     *     }
     * )
     */
    public function get_all_words(Request  $request){
        $get = Words::query();

        if (isset($request->category_id)){
            $get->where('category_id', $request->category_id);
        }

        if (isset($request->level_id)){
            $get->where('level_id', $request->level_id);
        }

        if (isset($request->text_id)){
            $get->where('text_id', $request->text_id);
        }




        if(isset($request->search)){
            $keyword =$request->search;
            $name_parts = explode(" ", $keyword);
            foreach ($name_parts as $part) {
                $get->orWhere(function ($query) use ($part) {
                    $query->where('word_ru', 'like', "%{$part}%")
                        ->orwhere('word_en', 'like', "%{$part}%")
                        ->orwhere('word_fr', 'like', "%{$part}%")
                    ;
                });
            }
        }



        $get = $get->simplepaginate(20);


        return response()->json([
           'status' => true,
           'data' => $get
        ],200);
    }


    /**
     * @OA\Post(
     *     path="/api/create_words",
     *     summary="Create Words",
     *     description="Create words with optional photo and audio",
     *     operationId="createWords",
     *     tags={"Admin Words"},
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
     *             @OA\Property(property="text_id", type="integer", description="Text ID (required)"),
     *             @OA\Property(property="word_ru", type="string", description="Word in Russian (required)"),
     *             @OA\Property(property="word_en", type="string", description="Word in English (required)"),
     *             @OA\Property(property="word_fr", type="string", description="Word in French (required)"),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Word Created",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Word created"),
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
    public function create_words(Request  $request){
        $rules=array(
            'photo' => 'image|mimes:jpeg,png,jpg,gif,webp',
            'audio_ru' => 'mimes:audio/mpeg,audio/wav,audio/mp3',
            'audio_en' => 'mimes:audio/mpeg,audio/wav,audio/mp3',
            'audio_fr' => 'mimes:audio/mpeg,audio/wav,audio/mp3',
            'category_id' => 'required|exists:categories,id',
            'levels_id' => 'required|exists:levels,id',
            'text_id' => 'required|exists:texts,id',
            'word_ru' => 'required',
            'word_en' => 'required',
            'word_fr' => 'required',
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

        Words::create([
            'category_id'  => $request->category_id ,
            'levels_id'  => $request->levels_id ,
            'text_id'  => $request->text_id ,
            'word_ru'  => $request->word_ru ,
            'word_en'  => $request->word_en ,
            'word_fr'  => $request->word_fr ,
            'audio_ru' => $audi_ru_name??null,
            'audio_en' => $audi_en_name??null ,
            'audio_fr' => $audi_fr_name??null,
            'photo' => $fileName??null,
        ]);


        return  response()->json([
           'status' => true,
           'message' => 'word created'
        ],200);
    }

    /**
     * @OA\Post(
     *     path="/api/update_word",
     *     summary="Admin Words",
     *     description="Update details of a word by its ID",
     *     operationId="updateWord",
     *     tags={"Admin Words"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="photo", type="string", format="binary", description="Updated image file (jpeg, png, jpg, gif, webp)"),
     *             @OA\Property(property="audio_ru", type="string", format="binary", description="Updated audio file in Russian (mpeg, wav, mp3)"),
     *             @OA\Property(property="audio_en", type="string", format="binary", description="Updated audio file in English (mpeg, wav, mp3)"),
     *             @OA\Property(property="audio_fr", type="string", format="binary", description="Updated audio file in French (mpeg, wav, mp3)"),
     *             @OA\Property(property="category_id", type="integer", description="Updated category ID (required)"),
     *             @OA\Property(property="word_id", type="integer", description="ID of the word to update (required)"),
     *             @OA\Property(property="levels_id", type="integer", description="Updated levels ID (required)"),
     *             @OA\Property(property="text_id", type="integer", description="Updated text ID (required)"),
     *             @OA\Property(property="word_ru", type="string", description="Updated word in Russian (required)"),
     *             @OA\Property(property="word_en", type="string", description="Updated word in English (required)"),
     *             @OA\Property(property="word_fr", type="string", description="Updated word in French (required)"),
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
     *             @OA\Property(property="status", type="boolean", example=false),
     *         ),
     *     ),
     *     security={
     *         {"bearerAuth": {}}
     *     }
     * )
     */
    public function update_word(Request  $request){
        $rules=array(
            'photo' => 'image|mimes:jpeg,png,jpg,gif,webp',
            'audio_ru' => 'mimes:audio/mpeg,audio/wav,audio/mp3',
            'audio_en' => 'mimes:audio/mpeg,audio/wav,audio/mp3',
            'audio_fr' => 'mimes:audio/mpeg,audio/wav,audio/mp3',
            'category_id' => 'required|exists:categories,id',
            'word_id' => 'required|exists:words,id',
            'levels_id' => 'required|exists:levels,id',
            'text_id' => 'required|exists:texts,id',
            'word_ru' => 'required',
            'word_en' => 'required',
            'word_fr' => 'required',
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

        $get = Words::where('id', $request->word_id)->first();


        $get->update([
            'category_id'  => $request->category_id ,
            'levels_id'  => $request->levels_id ,
            'text_id'  => $request->text_id ,
            'word_ru'  => $request->word_ru ,
            'word_en'  => $request->word_en ,
            'word_fr'  => $request->word_fr ,
            'audio_ru' => $audi_ru_name??$get->audio_ru,
            'audio_en' => $audi_en_name??$get->audio_en ,
            'audio_fr' => $audi_fr_name??$get->audio_fr,
            'photo' => $fileName??$get->photo,
        ]);


        return  response()->json([
           'status' => true,
           'message' => 'updated'
        ],200);
    }
    /**
     * @OA\Post(
     *     path="/api/delete_word",
     *     summary="Delete word",
     *     description="Delete a word by its ID",
     *     operationId="deleteWord",
     *     tags={"Admin Words"},
     *     @OA\Parameter(
     *         name="word_id",
     *         in="query",
     *         description="ID of the word to delete",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Deleted",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Deleted"),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Not Found",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="404 word"),
     *         ),
     *     ),
     *     security={
     *         {"bearerAuth": {}}
     *     }
     * )
     */

    public function delete_word(Request $request){
        $get = Words::where('id', $request->word_id)->first();

        if($get == null){
            return response()->json([
               'status' => false,
               'message' => '404 word'
            ],404);
        }


        if (file_exists(public_path("uploads/$get->photo"))) {
            unlink(public_path("uploads/$get->photo"));
        }

        if (file_exists(public_path("uploads/$get->audio_ru"))) {
            unlink(public_path("uploads/$get->audio_ru"));
        }


        if (file_exists(public_path("uploads/$get->audio_en"))) {
            unlink(public_path("uploads/$get->audio_en"));
        }

        if (file_exists(public_path("uploads/$get->audio_fr"))) {
            unlink(public_path("uploads/$get->audio_fr"));
        }

        $get->delete();

        return  response()->json([
           'status' => true,
           'message' => 'Deleted'
        ],200);
    }
}
