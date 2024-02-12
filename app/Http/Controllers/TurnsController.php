<?php

namespace App\Http\Controllers;

use App\Models\Turns;
use Illuminate\Http\Request;
use App\Events\EventTurn;
use Carbon\Carbon;

class TurnsController extends Controller
{
     /**
     * @OA\Get (
     *     path="/api/turns",
     *     operationId="all_turns",
     *     tags={"Turns"},
     *     security={{ "apiAuth": {} }},
     *     summary="All turns",
     *     description="All turns",
     *     @OA\Response(
     *         response=200,
     *         description="OK",
     *         @OA\JsonContent()
     *     ),
     *      @OA\Response(
     *          response=404,
     *          description="NOT FOUND",
     *          @OA\JsonContent()
     *      )
     * )
     */
    public function index()
    {
        $turns = Turns::orderBy('id', 'ASC')->get();
        return response()->json(["data"=>$turns],200);
    }

        /**
     * @OA\Get (
     *     path="/api/turns/{id}",
     *      operationId="turn_by_id",
     *     tags={"Turns"},
     *     security={{ "apiAuth": {} }},
     *     summary="Turn by id",
     *     description="Turn by id",
     *     @OA\Parameter(
     *         in="path",
     *         name="id",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="OK",
     *         @OA\JsonContent()
     *     ),
     *      @OA\Response(
     *          response=404,
     *          description="NOT FOUND",
     *          @OA\JsonContent()
     *      )
     * )
     */
    public function watch($id){
        try{
            $entity = Turns::find($id);
            return response()->json(["data"=>entity],200);
        }catch (Exception $e) {
            return response()->json(["data"=>"none"],200);
        }
    }

     /**
     * @OA\Post(
     *      path="/api/turns",
     *      operationId="store_turn",
     *      tags={"Turns"},
     *      security={{ "apiAuth": {} }},
     *      summary="Store turn",
     *      description="Store turn",
     *      @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *            @OA\Property(property="window", type="string", format="string", example="1"),
     *            @OA\Property(property="status", type="string", format="string", example="wait"),
     *         ),
     *      ),
     *     @OA\Response(
     *          response=200, description="Success",
     *          @OA\JsonContent(
     *             @OA\Property(property="status", type="integer", example=""),
     *             @OA\Property(property="data",type="object")
     *          )
     *       )
     *  )
     */
    public function register(Request $request)
    {
        $totalToday = Turns::whereDate('created_at', Carbon::today())->count();
        $turns = new Turns(request()->all());
        $turns->code = $totalToday;
        $turns->save();
        $msg = 'register_turn';
        event(new EventTurn($msg));
        return response()->json(["data"=>$turns],200);
    }

    /**
     * @OA\Post(
     *      path="/api/turns/update/{id}",
     *      operationId="update_turn",
     *      tags={"Turns"},
     *      security={{ "apiAuth": {} }},
     *      summary="Update turn",
     *      description="Update turn",
     *     @OA\Parameter(
     *         in="path",
     *         name="id",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *      @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *            required={"name"},
     *            @OA\Property(property="name", type="string", format="string", example="Name"),
     *         ),
     *      ),
     *     @OA\Response(
     *          response=200, description="Success",
     *          @OA\JsonContent(
     *             @OA\Property(property="status", type="integer", example=""),
     *             @OA\Property(property="data",type="object")
     *          )
     *       )
     *  )
     */
    public function update(Request $request, $id){
        try{
            $turn = Turns::find($id);
            $turn->update($request->all());

            if($request->status == 'call'){
                $msg = ['action'=>'call','turn'=>$turn->code,'puesto'=>$turn->window];
            }else{
                $msg = ['action'=>'wait'];
            }

            event(new EventTurn($msg));
            return response()->json(["data"=>"ok"],200);
        }catch (Exception $e) {
            return response()->json(["data"=>"none"],200);
        }
    }

        /**
     * @OA\Post(
     *      path="/api/turns/delete/{id}",
     *      operationId="delete_turn",
     *      tags={"Turns"},
     *     security={{ "apiAuth": {} }},
     *      summary="Delete turn",
     *      description="Delete turn",
     *    @OA\Parameter(
     *         in="path",
     *         name="id",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *          response=200, description="Success",
     *          @OA\JsonContent(
     *             @OA\Property(property="status", type="integer", example=""),
     *             @OA\Property(property="data",type="object")
     *          )
     *       )
     *  )
     */
    public function delete($id){
        try{
            $turn = Turns::destroy($id);
            $msg = 'delete_turn';
            event(new EventTurn($msg));
            return response()->json(["data"=>"ok"],200);
        }catch (Exception $e) {
            return response()->json(["data"=>"none"],200);
        }
    }
}
