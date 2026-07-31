<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use Illuminate\Http\Request;
use Validator;

class AcademicYearController extends Controller
{
    /**
     * @OA\Get(
     *      path="/api/academic-years",
     *      operationId="getAcademicYearsList",
     *      tags={"1. Core System - AcademicYear"},
     *      summary="Get list of AcademicYears",
     *      description="Returns list of AcademicYears",
     *      security={{"sanctum":{}}},
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(
     *              @OA\Property(property="success", type="boolean", example=true),
     *              @OA\Property(
     *                  property="data",
     *                  type="array",
     *                  @OA\Items(
     *                  @OA\Property(property="id", type="integer"),
     *                  @OA\Property(property="name", type="string"),
     *                  @OA\Property(property="start_date", type="string"),
     *                  @OA\Property(property="end_date", type="string"),
     *                  @OA\Property(property="is_current", type="boolean"),
     *                  @OA\Property(property="created_at", type="string"),
     *                  @OA\Property(property="updated_at", type="string"),
     *                  @OA\Property(property="deleted_at", type="string")
     *                  )
     *              )
     *          )
     *       ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *      ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *      )
     *     )
     */

    public function index()
    {
        $years = AcademicYear::latest()->get();

        return response()->json([
            'success'=>true,
            'data'=>$years
        ]);
    }
    /**
     * @OA\Post(
     *      path="/api/academic-years",
     *      operationId="storeAcademicYear",
     *      tags={"1. Core System - AcademicYear"},
     *      summary="Store new academic year",
     *      description="Returns saved academic year data",
     *      security={{"sanctum":{}}},
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"name","start_date","end_date"},
     *              @OA\Property(property="name", type="string", example="name"),
     *              @OA\Property(property="start_date", type="string", example="start_date"),
     *              @OA\Property(property="end_date", type="string", example="end_date")
     *          )
     *      ),
     *      @OA\Response(
     *          response=201,
     *          description="Successful operation",
     *          @OA\JsonContent(
     *              @OA\Property(property="success", type="boolean", example=true),
     *              @OA\Property(property="message", type="string", example="Success"),
     *              @OA\Property(
     *                  property="data",
     *                  type="object",
     *                  @OA\Property(property="id", type="integer"),
     *                  @OA\Property(property="name", type="string"),
     *                  @OA\Property(property="start_date", type="string"),
     *                  @OA\Property(property="end_date", type="string"),
     *                  @OA\Property(property="is_current", type="boolean"),
     *                  @OA\Property(property="created_at", type="string"),
     *                  @OA\Property(property="updated_at", type="string"),
     *                  @OA\Property(property="deleted_at", type="string")
     *              )
     *          )
     *       ),
     *      @OA\Response(
     *          response=400,
     *          description="Bad Request"
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *      ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *      )
     * )
     */

    public function store(Request $request)
    {

        $validator = Validator::make($request->all(),[
            'name'=>'required',
            'start_date'=>'required',
            'end_date'=>'required'
        ]);

        if($validator->fails()){
            return response()->json([
                'errors'=>$validator->errors()
            ],422);
        }

        $year = AcademicYear::create($request->all());

        return response()->json([
            'success'=>true,
            'data'=>$year
        ]);
    }
    /**
     * @OA\Get(
     *      path="/api/academic-years/{id}",
     *      operationId="getAcademicYearById",
     *      tags={"1. Core System - AcademicYear"},
     *      summary="Get academic year information",
     *      description="Returns academic year data",
     *      security={{"sanctum":{}}},
     *      @OA\Parameter(
     *          name="id",
     *          description="AcademicYear id",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(
     *              @OA\Property(property="success", type="boolean", example=true),
     *              @OA\Property(property="message", type="string", example="Success"),
     *              @OA\Property(
     *                  property="data",
     *                  type="object",
     *                  @OA\Property(property="id", type="integer"),
     *                  @OA\Property(property="name", type="string"),
     *                  @OA\Property(property="start_date", type="string"),
     *                  @OA\Property(property="end_date", type="string"),
     *                  @OA\Property(property="is_current", type="boolean"),
     *                  @OA\Property(property="created_at", type="string"),
     *                  @OA\Property(property="updated_at", type="string"),
     *                  @OA\Property(property="deleted_at", type="string")
     *              )
     *          )
     *       ),
     *      @OA\Response(
     *          response=400,
     *          description="Bad Request"
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *      ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *      )
     * )
     */

    public function show($id)
    {
        $year = AcademicYear::find($id);

        return response()->json($year);
    }
    /**
     * @OA\Put(
     *      path="/api/academic-years/{id}",
     *      operationId="updateAcademicYear",
     *      tags={"1. Core System - AcademicYear"},
     *      summary="Update existing academic year",
     *      description="Returns updated academic year data",
     *      security={{"sanctum":{}}},
     *      @OA\Parameter(
     *          name="id",
     *          description="AcademicYear id",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"name","start_date","end_date"},
     *              @OA\Property(property="name", type="string", example="name"),
     *              @OA\Property(property="start_date", type="string", example="start_date"),
     *              @OA\Property(property="end_date", type="string", example="end_date")
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(
     *              @OA\Property(property="success", type="boolean", example=true),
     *              @OA\Property(property="message", type="string", example="Success"),
     *              @OA\Property(
     *                  property="data",
     *                  type="object",
     *                  @OA\Property(property="id", type="integer"),
     *                  @OA\Property(property="name", type="string"),
     *                  @OA\Property(property="start_date", type="string"),
     *                  @OA\Property(property="end_date", type="string"),
     *                  @OA\Property(property="is_current", type="boolean"),
     *                  @OA\Property(property="created_at", type="string"),
     *                  @OA\Property(property="updated_at", type="string"),
     *                  @OA\Property(property="deleted_at", type="string")
     *              )
     *          )
     *       ),
     *      @OA\Response(
     *          response=400,
     *          description="Bad Request"
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *      ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Resource Not Found"
     *      )
     * )
     */

    public function update(Request $request,$id)
    {
        $year = AcademicYear::findOrFail($id);

        $validator = Validator::make($request->all(),[
            'name'=>'required',
            'start_date'=>'required',
            'end_date'=>'required'
        ]);

        if($validator->fails()){
            return response()->json([
                'errors'=>$validator->errors()
            ],422);
        }

        $year->update($request->all());

        return response()->json([
            'success'=>true,
            'message'=>'Updated'
        ]);
    }
    /**
     * @OA\Delete(
     *      path="/api/academic-years/{id}",
     *      operationId="deleteAcademicYear",
     *      tags={"1. Core System - AcademicYear"},
     *      summary="Delete existing academic year",
     *      description="Deletes a record and returns no content",
     *      security={{"sanctum":{}}},
     *      @OA\Parameter(
     *          name="id",
     *          description="AcademicYear id",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\Response(
     *          response=204,
     *          description="Successful operation"
     *       ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *      ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Resource Not Found"
     *      )
     * )
     */

    public function destroy($id)
    {
        $year = AcademicYear::findOrFail($id);

        $year->delete();

        return response()->json([
            'success'=>true,
            'message'=>'Deleted'
        ]);
    }
}