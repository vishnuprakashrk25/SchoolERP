<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Classes;
use Illuminate\Http\Request;
use App\Models\Section;
use Illuminate\Support\Facades\Validator;

class ClassController extends Controller
{
    /**
     * @OA\Get(
     *      path="/api/classes",
     *      operationId="getClassesList",
     *      tags={"3. Academic Infrastructure - Class"},
     *      summary="Get list of Classes",
     *      description="Returns list of Classes",
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
     *                  @OA\Property(property="branch_id", type="integer"),
     *                  @OA\Property(property="academic_year_id", type="integer"),
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
        $classes = Classes::with(['academicYear', 'sections'])->get();

        return response()->json([
            'success'=>true,
            'data'=>$classes
        ]);
    }
    /**
     * @OA\Post(
     *      path="/api/classes",
     *      operationId="storeClass",
     *      tags={"3. Academic Infrastructure - Class"},
     *      summary="Store new class",
     *      description="Returns saved class data",
     *      security={{"sanctum":{}}},
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"name","academic_year_id","branch_id"},
     *              @OA\Property(property="name", type="string", example="name"),
     *              @OA\Property(property="academic_year_id", type="integer", example="1"),
     *              @OA\Property(property="branch_id", type="integer", example="1")
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
     *                  @OA\Property(property="branch_id", type="integer"),
     *                  @OA\Property(property="academic_year_id", type="integer"),
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
            'academic_year_id'=>'required',
             'branch_id'=>'required',
        ]);

        if($validator->fails()){
            return response()->json([
                'errors'=>$validator->errors()
            ],422);
        }

        $class = Classes::create($request->all());

        if ($request->has('sections') && is_array($request->sections)) {
            foreach ($request->sections as $sectionName) {
                Section::create([
                    'class_id' => $class->id,
                    'name' => $sectionName
                ]);
            }
        }

        return response()->json([
            'success'=>true,
            'data'=>$class
        ]);
    }
    /**
     * @OA\Get(
     *      path="/api/classes/{id}",
     *      operationId="getClassById",
     *      tags={"3. Academic Infrastructure - Class"},
     *      summary="Get class information",
     *      description="Returns class data",
     *      security={{"sanctum":{}}},
     *      @OA\Parameter(
     *          name="id",
     *          description="Class id",
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
     *                  @OA\Property(property="branch_id", type="integer"),
     *                  @OA\Property(property="academic_year_id", type="integer"),
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
        $class = Classes::findOrFail($id);

        return response()->json($class);
    }
    /**
     * @OA\Put(
     *      path="/api/classes/{id}",
     *      operationId="updateClass",
     *      tags={"3. Academic Infrastructure - Class"},
     *      summary="Update existing class",
     *      description="Returns updated class data",
     *      security={{"sanctum":{}}},
     *      @OA\Parameter(
     *          name="id",
     *          description="Class id",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"name","academic_year_id","branch_id"},
     *              @OA\Property(property="name", type="string", example="name"),
     *              @OA\Property(property="academic_year_id", type="integer", example="1"),
     *              @OA\Property(property="branch_id", type="integer", example="1")
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
     *                  @OA\Property(property="branch_id", type="integer"),
     *                  @OA\Property(property="academic_year_id", type="integer"),
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
        $class = Classes::findOrFail($id);

        $validator = Validator::make($request->all(),[
            'name'=>'required',
            'academic_year_id'=>'required',
             'branch_id'=>'required',
        ]);

        if($validator->fails()){
            return response()->json([
                'errors'=>$validator->errors()
            ],422);
        }

        $class->update($request->all());

        if ($request->has('sections') && is_array($request->sections)) {
            $class->sections()->delete();
            foreach ($request->sections as $sectionName) {
                Section::create([
                    'class_id' => $class->id,
                    'name' => $sectionName
                ]);
            }
        }

        return response()->json([
            'success'=>true,
            'message'=>'Updated'
        ]);
    }
    /**
     * @OA\Delete(
     *      path="/api/classes/{id}",
     *      operationId="deleteClass",
     *      tags={"3. Academic Infrastructure - Class"},
     *      summary="Delete existing class",
     *      description="Deletes a record and returns no content",
     *      security={{"sanctum":{}}},
     *      @OA\Parameter(
     *          name="id",
     *          description="Class id",
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
        $class = Classes::findOrFail($id);

        $class->delete();

        return response()->json([
            'success'=>true,
            'message'=>'Deleted'
        ]);
    }
}