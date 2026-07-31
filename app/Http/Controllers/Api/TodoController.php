<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Todo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TodoController extends Controller
{
    /**
     * @OA\Get(
     *      path="/api/todos",
     *      operationId="getTodosList",
     *      tags={"Dashboard - Todos"},
     *      summary="Get list of todos for authenticated user",
     *      security={{"sanctum":{}}},
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(
     *              @OA\Property(property="success", type="boolean", example=true),
     *              @OA\Property(property="data", type="array", @OA\Items(type="object"))
     *          )
     *      )
     * )
     */
    public function index(Request $request)
    {
        $todos = Todo::where('user_id', $request->user()->id)
            ->orderBy('is_completed', 'asc')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $todos
        ]);
    }

    /**
     * @OA\Post(
     *      path="/api/todos",
     *      operationId="storeTodo",
     *      tags={"Dashboard - Todos"},
     *      summary="Store new todo",
     *      security={{"sanctum":{}}},
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"title"},
     *              @OA\Property(property="title", type="string", example="Buy groceries"),
     *              @OA\Property(property="date", type="string", format="date", example="2026-05-03")
     *          )
     *      ),
     *      @OA\Response(
     *          response=201,
     *          description="Successful operation",
     *          @OA\JsonContent(
     *              @OA\Property(property="success", type="boolean", example=true),
     *              @OA\Property(property="message", type="string"),
     *              @OA\Property(property="data", type="object")
     *          )
     *      )
     * )
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'date' => 'nullable|date',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $todo = Todo::create([
            'user_id' => $request->user()->id,
            'title' => $request->title,
            'date' => $request->date,
            'is_completed' => false,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Todo created successfully',
            'data' => $todo
        ], 201);
    }

    /**
     * @OA\Put(
     *      path="/api/todos/{id}",
     *      operationId="updateTodo",
     *      tags={"Dashboard - Todos"},
     *      summary="Update existing todo",
     *      security={{"sanctum":{}}},
     *      @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              @OA\Property(property="title", type="string"),
     *              @OA\Property(property="is_completed", type="boolean"),
     *              @OA\Property(property="date", type="string", format="date")
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(
     *              @OA\Property(property="success", type="boolean", example=true),
     *              @OA\Property(property="message", type="string"),
     *              @OA\Property(property="data", type="object")
     *          )
     *      )
     * )
     */
    public function update(Request $request, $id)
    {
        $todo = Todo::where('user_id', $request->user()->id)->findOrFail($id);

        $validator = Validator::make($request->all(), [
            'title' => 'sometimes|string|max:255',
            'is_completed' => 'sometimes|boolean',
            'date' => 'sometimes|nullable|date',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $todo->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Todo updated successfully',
            'data' => $todo
        ]);
    }

    /**
     * @OA\Delete(
     *      path="/api/todos/{id}",
     *      operationId="deleteTodo",
     *      tags={"Dashboard - Todos"},
     *      summary="Delete existing todo",
     *      security={{"sanctum":{}}},
     *      @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *      @OA\Response(response=200, description="Successful operation")
     * )
     */
    public function destroy(Request $request, $id)
    {
        $todo = Todo::where('user_id', $request->user()->id)->findOrFail($id);
        $todo->delete();

        return response()->json([
            'success' => true,
            'message' => 'Todo deleted successfully'
        ]);
    }
}
