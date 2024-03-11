<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Agent;
use Validator;
class AgentController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/admin/create_agent",
     *     summary="Create a new agent",
     *     description="Creates a new agent with the specified email, name, surname, and phone.",
     *     tags={"Admin Agents"},
     *     security={
     *         {"passport": {}}
     *     },
     *     @OA\RequestBody(
     *         required=true,
     *         description="Agent details",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(property="email", type="string", format="email", description="Agent's email"),
     *                 @OA\Property(property="name", type="string", description="Agent's name"),
     *                 @OA\Property(property="surname", type="string", description="Agent's surname"),
     *                 @OA\Property(property="phone", type="string", description="Agent's phone number"),
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Agent created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", description="Agent created successfully"),
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
    public function create_agent(Request $request){
        $rules=array(
            'email' => 'required|email',
            'name' => 'required',
            'surname' => 'required',
            'phone' => 'required'
        );
        $validator=Validator::make($request->all(),$rules);
        if($validator->fails())
        {
            return response()->json([
                'status' => false,
                'message' =>$validator->errors()
            ],400);
        }

        Agent::create([
           'name' => $request->name,
           'surname' => $request->surname,
           'email' => $request->email,
           'phone' => $request->phone,
        ]);



        return response()->json([
           'status' => true,
           'message' => 'Agent Created'
        ],200);
    }

    /**
     * @OA\Post(
     *     path="/api/admin/update_agent",
     *     summary="Update agent information",
     *     description="Updates the information of an existing agent based on the provided agent_id.",
     *     tags={"Admin Agents"},
     *     security={
     *         {"passport": {}}
     *     },
     *     @OA\RequestBody(
     *         required=true,
     *         description="Agent details to update",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(property="email", type="string", format="email", description="New email for the agent"),
     *                 @OA\Property(property="name", type="string", description="New name for the agent"),
     *                 @OA\Property(property="surname", type="string", description="New surname for the agent"),
     *                 @OA\Property(property="phone", type="string", description="New phone number for the agent"),
     *                 @OA\Property(property="agent_id", type="integer", description="ID of the agent to update"),
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Agent information updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", description="Agent updated successfully"),
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

    public function update_agent(Request $request){
        $rules=array(
            'email' => 'required|email',
            'name' => 'required',
            'surname' => 'required',
            'phone' => 'required',
            'agent_id' => 'required|exists:agents,id'
        );
        $validator=Validator::make($request->all(),$rules);
        if($validator->fails())
        {
            return response()->json([
                'status' => false,
                'message' =>$validator->errors()
            ],400);
        }


        Agent::where('id', $request->agent_id)->update([
            'name' => $request->name,
            'surname' => $request->surname,
            'email' => $request->email,
            'phone' => $request->phone,
        ]);

        return response()->json([
           'status' => true,
           'message' => 'updated'
        ],200);
    }


    /**
     * @OA\Get(
     *     path="/api/admin/single_page_agent",
     *     summary="Get information of a single agent",
     *     description="Retrieves information of a single agent based on the provided agent_id.",
     *     tags={"Admin Agents"},
     *     security={
     *         {"passport": {}}
     *     },
     *     @OA\Parameter(
     *         name="agent_id",
     *         in="query",
     *         required=true,
     *         description="ID of the agent to retrieve",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Agent information retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Agent not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", description="Agent Not Found"),
     *         )
     *     ),
     * )
     */
    public function single_page_agent(Request $request){
        $get = Agent::where('id', $request->agent_id)->first();

        if ($get == null){
            return response()->json([
               'status' => false,
               'message'  =>'Agent Not Found'
            ],404);
        }



        return response()->json([
           'status' => true,
           'data' => $get
        ],200);
    }

    /**
     * @OA\Get(
     *     path="/api/admin/all_agents",
     *     summary="Get all agents with optional search",
     *     description="Retrieves a paginated list of all agents with optional search functionality.",
     *     tags={"Admin Agents"},
     *     security={
     *         {"passport": {}}
     *     },
     *     @OA\Parameter(
     *         name="search",
     *         in="query",
     *         description="Search keyword to filter agents by name, surname, phone, or email",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Agents retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *         )
     *     ),
     * )
     */

    public function all_agents(Request $request){
        $get = Agent::query();
        if (isset($request->search)){
            $keyword =$request->search;
            $name_parts = explode(" ", $keyword);
            foreach ($name_parts as $part) {
                $get->orWhere(function ($query) use ($part) {
                    $query->where('name', 'like', "%{$part}%")
                        ->orwhere('surname', 'like', "%{$part}%")
                        ->orwhere('phone', 'like', "%{$part}%")
                        ->orwhere('email', 'like', "%{$part}%")
                    ;
                });
            }
        }
        $get = $get->simplepaginate(10);
        return response()->json([
           'status'=> true,
           'data' => $get
        ],200);
    }




}
