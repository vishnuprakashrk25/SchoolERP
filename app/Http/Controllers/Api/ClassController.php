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
     *                  @OA\Property(property="academic_category_id", type="integer", nullable=true, example=6),
     *                  @OA\Property(property="category_id", type="integer", nullable=true, example=6),
     *                  @OA\Property(
     *                      property="academic_category",
     *                      type="object",
     *                      nullable=true,
     *                      @OA\Property(property="id", type="integer"),
     *                      @OA\Property(property="name", type="string"),
     *                      @OA\Property(property="description", type="string")
     *                  ),
     *                  @OA\Property(
     *                      property="category",
     *                      type="object",
     *                      nullable=true,
     *                      @OA\Property(property="id", type="integer"),
     *                      @OA\Property(property="name", type="string")
     *                  ),
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
        $classes = Classes::with(['academicYear', 'academicCategory:id,name,description', 'category:id,name', 'sections'])->get();

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
     *              required={"name"},
     *              @OA\Property(property="name", type="string", example="Class 3"),
     *              @OA\Property(property="academic_year_id", type="integer", nullable=true, example=1),
     *              @OA\Property(property="branch_id", type="integer", nullable=true, example=1),
     *              @OA\Property(property="academic_category_id", type="integer", nullable=true, example=6, description="Academic category ID (e.g. Rainbow - 3, Planet, Galaxy HS, etc.)"),
     *              @OA\Property(property="category_id", type="integer", nullable=true, example=6, description="Alias for academic_category_id")
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
     *                  @OA\Property(property="academic_category_id", type="integer", nullable=true),
     *                  @OA\Property(property="category_id", type="integer", nullable=true),
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
        $input = $request->all();
        foreach (['branch_id', 'academic_year_id', 'category_id', 'academic_category_id'] as $k) {
            if (array_key_exists($k, $input) && ($input[$k] === '' || (is_string($input[$k]) && trim($input[$k]) === ''))) {
                $input[$k] = null;
            }
        }
        if (isset($input['category_id']) && !isset($input['academic_category_id'])) {
            $input['academic_category_id'] = $input['category_id'];
        } elseif (isset($input['academic_category_id']) && !isset($input['category_id'])) {
            $input['category_id'] = $input['academic_category_id'];
        }

        $validator = Validator::make($input, [
            'name' => 'required',
            'academic_year_id' => 'nullable|exists:academic_years,id',
            'branch_id' => 'nullable|exists:branches,id',
            'academic_category_id' => 'nullable|exists:academic_categories,id',
            'category_id' => 'nullable|exists:academic_categories,id',
            'sections' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }

        $data = $input;
        if (!$request->filled('branch_id')) {
            $data['branch_id'] = ($request->user() && $request->user()->branch_id) ? $request->user()->branch_id : null;
        }

        $class = Classes::create($data);

        if ($request->has('sections') && is_array($request->sections)) {
            foreach ($request->sections as $sectionName) {
                Section::create([
                    'class_id' => $class->id,
                    'name' => $sectionName
                ]);
            }
        }

        $class->load(['academicYear', 'category:id,name', 'sections']);

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
     *                  @OA\Property(property="category_id", type="integer", nullable=true),
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
        $class = Classes::with(['academicYear', 'category:id,name', 'sections'])->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $class
        ]);
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
     *              required={"name"},
     *              @OA\Property(property="name", type="string", example="name"),
     *              @OA\Property(property="academic_year_id", type="integer", nullable=true, example=1),
     *              @OA\Property(property="branch_id", type="integer", nullable=true, example=1),
     *              @OA\Property(property="category_id", type="integer", nullable=true, example=1, description="Student category ID")
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
     *                  @OA\Property(property="category_id", type="integer", nullable=true),
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

        $input = $request->all();
        foreach (['branch_id', 'academic_year_id', 'category_id', 'academic_category_id'] as $k) {
            if (array_key_exists($k, $input) && ($input[$k] === '' || (is_string($input[$k]) && trim($input[$k]) === ''))) {
                $input[$k] = null;
            }
        }
        if (isset($input['category_id']) && !isset($input['academic_category_id'])) {
            $input['academic_category_id'] = $input['category_id'];
        } elseif (isset($input['academic_category_id']) && !isset($input['category_id'])) {
            $input['category_id'] = $input['academic_category_id'];
        }

        $validator = Validator::make($input, [
            'name' => 'required',
            'academic_year_id' => 'nullable|exists:academic_years,id',
            'branch_id' => 'nullable|exists:branches,id',
            'academic_category_id' => 'nullable|exists:academic_categories,id',
            'category_id' => 'nullable|exists:academic_categories,id',
            'sections' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }

        $class->update($input);

        if ($request->has('sections') && is_array($request->sections)) {
            $existingSections = $class->sections()->get()->keyBy('name');
            $incomingNames = array_unique(array_filter(array_map('trim', $request->sections)));

            // Add new sections that do not exist yet
            foreach ($incomingNames as $secName) {
                if (!$existingSections->has($secName)) {
                    Section::create([
                        'class_id' => $class->id,
                        'name' => $secName
                    ]);
                }
            }

            // Only delete omitted sections if no active students are attached
            foreach ($existingSections as $secName => $section) {
                if (!in_array($secName, $incomingNames)) {
                    $hasStudents = \App\Models\Student::where('section_id', $section->id)->exists();
                    if (!$hasStudents) {
                        $section->delete();
                    }
                }
            }
        }

        $class->load(['academicYear', 'academicCategory:id,name,description', 'category:id,name', 'sections']);

        return response()->json([
            'success'=>true,
            'message'=>'Updated',
            'data'=>$class
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