<?php

namespace App\Http\Controllers\Api\FrontOffice;

use App\Http\Controllers\Controller;
use App\Models\PostalReceive;
use App\Models\PostalDispatch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PostalController extends Controller
{
    /**
     * @OA\Get(
     *      path="/api/front-office/postal-receives",
     *      operationId="getPostalReceivesList",
     *      tags={"1. Reception & Admission - Front Office"},
     *      summary="Get list of received postals",
     *      security={{"sanctum":{}}},
     *      @OA\Response(response=200, description="Successful operation")
     * )
     */
    public function getReceives()
    {
        return response()->json([
            'success' => true,
            'data' => PostalReceive::latest()->get()
        ]);
    }

    /**
     * @OA\Post(
     *      path="/api/front-office/postal-receives",
     *      operationId="storePostalReceive",
     *      tags={"1. Reception & Admission - Front Office"},
     *      summary="Store new received postal",
     *      security={{"sanctum":{}}},
     *      @OA\RequestBody(required=true, @OA\JsonContent(required={"from_title", "reference_no"}, type="object")),
     *      @OA\Response(response=201, description="Created")
     * )
     */
    public function storeReceive(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'from_title' => 'required|string|max:255',
            'reference_no' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $postal = PostalReceive::create($request->all());

        return response()->json(['success' => true, 'data' => $postal], 201);
    }

    /**
     * @OA\Get(
     *      path="/api/front-office/postal-dispatches",
     *      operationId="getPostalDispatchesList",
     *      tags={"1. Reception & Admission - Front Office"},
     *      summary="Get list of dispatched postals",
     *      security={{"sanctum":{}}},
     *      @OA\Response(response=200, description="Successful operation")
     * )
     */
    public function getDispatches()
    {
        return response()->json([
            'success' => true,
            'data' => PostalDispatch::latest()->get()
        ]);
    }

    /**
     * @OA\Post(
     *      path="/api/front-office/postal-dispatches",
     *      operationId="storePostalDispatch",
     *      tags={"1. Reception & Admission - Front Office"},
     *      summary="Store new dispatched postal",
     *      security={{"sanctum":{}}},
     *      @OA\RequestBody(required=true, @OA\JsonContent(required={"to_title", "reference_no"}, type="object")),
     *      @OA\Response(response=201, description="Created")
     * )
     */
    public function storeDispatch(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'to_title' => 'required|string|max:255',
            'reference_no' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $postal = PostalDispatch::create($request->all());

        return response()->json(['success' => true, 'data' => $postal], 201);
    }

    /**
     * @OA\Delete(
     *      path="/api/front-office/postal-receives/{id}",
     *      operationId="deletePostalReceive",
     *      tags={"1. Reception & Admission - Front Office"},
     *      summary="Delete received postal",
     *      security={{"sanctum":{}}},
     *      @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *      @OA\Response(response=200, description="Deleted")
     * )
     */
    public function deleteReceive($id)
    {
        PostalReceive::findOrFail($id)->delete();
        return response()->json(['success' => true, 'message' => 'Deleted']);
    }

    /**
     * @OA\Delete(
     *      path="/api/front-office/postal-dispatches/{id}",
     *      operationId="deletePostalDispatch",
     *      tags={"1. Reception & Admission - Front Office"},
     *      summary="Delete dispatched postal",
     *      security={{"sanctum":{}}},
     *      @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *      @OA\Response(response=200, description="Deleted")
     * )
     */
    public function deleteDispatch($id)
    {
        PostalDispatch::findOrFail($id)->delete();
        return response()->json(['success' => true, 'message' => 'Deleted']);
    }
}
