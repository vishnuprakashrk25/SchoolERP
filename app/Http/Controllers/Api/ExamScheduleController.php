<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ExamSchedule;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class ExamScheduleController extends Controller
{
    /**
     * @OA\Get(
     *      path="/api/exam-schedules",
     *      operationId="getExamSchedulesList",
     *      tags={"6. Examinations & Assessment - Exam Schedule"},
     *      summary="Get list of Exam Schedules",
     *      description="Returns list of Exam Schedules filterable by exam_id, class_id, and section_id.",
     *      security={{"sanctum":{}}},
     *      @OA\Parameter(name="exam_id", in="query", required=false, @OA\Schema(type="integer")),
     *      @OA\Parameter(name="exam_type_id", in="query", required=false, @OA\Schema(type="integer")),
     *      @OA\Parameter(name="class_id", in="query", required=false, @OA\Schema(type="integer")),
     *      @OA\Parameter(name="section_id", in="query", required=false, @OA\Schema(type="integer")),
     *      @OA\Parameter(name="subject_id", in="query", required=false, @OA\Schema(type="integer")),
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
        $examId = $request->input('exam_id') ?? $request->input('exam_type_id');
        $classId = $request->input('class_id');
        $sectionId = $request->input('section_id');

        $query = ExamSchedule::with([
            'exam:id,name',
            'examType:id,name',
            'class:id,name',
            'section:id,name',
            'subject:id,name,subject_code',
            'teacher:id,first_name,last_name,full_name,staff_code'
        ]);

        if (!empty($examId)) {
            $query->where(function ($q) use ($examId) {
                $q->where('exam_id', $examId)
                  ->orWhere('exam_type_id', $examId);
            });
        }
        if (!empty($classId)) {
            $query->where('class_id', $classId);
        }
        if (!empty($sectionId)) {
            $query->where('section_id', $sectionId);
        }
        if ($request->filled('subject_id')) {
            $query->where('subject_id', $request->input('subject_id'));
        }

        $schedules = $query->orderBy('exam_date')->orderBy('start_time')->get();

        $formatted = $schedules->map(function ($sch) {
            $dateDay = '';
            if ($sch->exam_date) {
                $cDate = Carbon::parse($sch->exam_date);
                $dateDay = $cDate->format('d/m/Y') . ' ' . $cDate->format('l');
            }

            $timeFormatted = '';
            if ($sch->start_time && $sch->end_time) {
                $timeFormatted = Carbon::parse($sch->start_time)->format('h:i A') . ' to ' . Carbon::parse($sch->end_time)->format('h:i A');
            } elseif ($sch->start_time) {
                $timeFormatted = Carbon::parse($sch->start_time)->format('h:i A');
            }

            $classSection = ($sch->class?->name ?? '') . ($sch->section ? ' ' . $sch->section->name : '');

            return [
                'id'              => $sch->id,
                'exam_id'         => $sch->exam_id ?? $sch->exam_type_id,
                'exam_name'       => $sch->exam?->name ?? $sch->examType?->name,
                'class_id'        => $sch->class_id,
                'class_name'      => $sch->class?->name,
                'section_id'      => $sch->section_id,
                'section_name'    => $sch->section?->name,
                'class_section'   => trim($classSection),
                'subject_id'      => $sch->subject_id,
                'subject_name'    => $sch->subject?->name,
                'subject_code'    => $sch->subject?->subject_code,
                'teacher_id'      => $sch->teacher_id,
                'teacher_name'    => $sch->teacher?->full_name ?? ($sch->teacher ? trim($sch->teacher->first_name . ' ' . $sch->teacher->last_name) : null),
                'date'            => $sch->exam_date ? $sch->exam_date->format('Y-m-d') : null,
                'date_and_day'    => $dateDay,
                'start_time'      => $sch->start_time,
                'end_time'        => $sch->end_time,
                'time'            => $timeFormatted,
                'duration'        => $sch->duration ?? '45 Minutes',
                'room'            => $sch->room,
            ];
        });

        return response()->json([
            'success' => true,
            'count'   => $formatted->count(),
            'data'    => $formatted
        ]);
    }

    /**
     * @OA\Post(
     *      path="/api/exam-schedules",
     *      operationId="storeExamSchedule",
     *      tags={"6. Examinations & Assessment - Exam Schedule"},
     *      summary="Store new Exam Schedule",
     *      security={{"sanctum":{}}},
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"class_id", "subject_id"},
     *              @OA\Property(property="exam_id", type="integer", example=1),
     *              @OA\Property(property="class_id", type="integer", example=5),
     *              @OA\Property(property="section_id", type="integer", example=2),
     *              @OA\Property(property="subject_id", type="integer", example=3),
     *              @OA\Property(property="teacher_id", type="integer", example=10),
     *              @OA\Property(property="exam_date", type="string", format="date", example="2026-11-20"),
     *              @OA\Property(property="start_time", type="string", example="09:00:00"),
     *              @OA\Property(property="end_time", type="string", example="09:45:00"),
     *              @OA\Property(property="duration", type="string", example="45 Minutes"),
     *              @OA\Property(property="room", type="string", example="235432")
     *          )
     *      ),
     *      @OA\Response(response=201, description="Exam Schedule created successfully")
     * )
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'exam_id'    => 'nullable|integer',
            'class_id'   => 'required|integer',
            'section_id' => 'nullable|integer',
            'subject_id' => 'required|integer',
            'teacher_id' => 'nullable|integer',
            'exam_date'  => 'nullable|date',
            'start_time' => 'nullable|string',
            'end_time'   => 'nullable|string',
            'duration'   => 'nullable|string',
            'room'       => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors()
            ], 422);
        }

        $data = $request->only([
            'exam_id', 'class_id', 'section_id', 'subject_id', 'teacher_id',
            'exam_date', 'start_time', 'end_time', 'duration', 'room'
        ]);
        $data['exam_type_id'] = $data['exam_id'] ?? null;

        $item = ExamSchedule::create($data);
        $item->load(['class', 'section', 'subject', 'teacher', 'exam']);

        return response()->json([
            'success' => true,
            'message' => 'Exam Schedule created successfully',
            'data'    => $item
        ], 201);
    }

    /**
     * @OA\Post(
     *      path="/api/exam-schedules/bulk",
     *      operationId="bulkStoreExamSchedule",
     *      tags={"6. Examinations & Assessment - Exam Schedule"},
     *      summary="Bulk store / update Exam Schedules for a class and exam",
     *      description="Accepts an array of schedule rows and saves them all at once.",
     *      security={{"sanctum":{}}},
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"schedules"},
     *              @OA\Property(
     *                  property="schedules",
     *                  type="array",
     *                  @OA\Items(
     *                      @OA\Property(property="exam_id", type="integer"),
     *                      @OA\Property(property="class_id", type="integer"),
     *                      @OA\Property(property="section_id", type="integer"),
     *                      @OA\Property(property="subject_id", type="integer"),
     *                      @OA\Property(property="teacher_id", type="integer"),
     *                      @OA\Property(property="exam_date", type="string", format="date"),
     *                      @OA\Property(property="start_time", type="string"),
     *                      @OA\Property(property="end_time", type="string"),
     *                      @OA\Property(property="duration", type="string"),
     *                      @OA\Property(property="room", type="string")
     *                  )
     *              )
     *          )
     *      ),
     *      @OA\Response(response=200, description="Schedules saved successfully")
     * )
     */
    public function bulkStore(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'schedules'               => 'required|array|min:1',
            'schedules.*.class_id'    => 'required|integer',
            'schedules.*.subject_id'  => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors()
            ], 422);
        }

        $saved = [];
        foreach ($request->input('schedules') as $row) {
            $examId = $row['exam_id'] ?? $request->input('exam_id');
            $data = [
                'exam_id'      => $examId,
                'exam_type_id' => $examId,
                'class_id'     => $row['class_id'],
                'section_id'   => $row['section_id'] ?? $request->input('section_id'),
                'subject_id'   => $row['subject_id'],
                'teacher_id'   => $row['teacher_id'] ?? null,
                'exam_date'    => $row['exam_date'] ?? null,
                'start_time'   => $row['start_time'] ?? null,
                'end_time'     => $row['end_time'] ?? null,
                'duration'     => $row['duration'] ?? null,
                'room'         => $row['room'] ?? null,
            ];

            if (!empty($row['id'])) {
                $item = ExamSchedule::find($row['id']);
                if ($item) {
                    $item->update($data);
                    $saved[] = $item;
                    continue;
                }
            }

            $saved[] = ExamSchedule::create($data);
        }

        return response()->json([
            'success' => true,
            'message' => count($saved) . ' Exam Schedule(s) saved successfully',
            'count'   => count($saved),
            'data'    => $saved
        ]);
    }

    /**
     * @OA\Get(
     *      path="/api/exam-schedules/{id}",
     *      operationId="getExamScheduleById",
     *      tags={"6. Examinations & Assessment - Exam Schedule"},
     *      summary="Get Exam Schedule information",
     *      security={{"sanctum":{}}},
     *      @OA\Parameter(name="id", required=true, in="path", @OA\Schema(type="integer")),
     *      @OA\Response(response=200, description="Successful operation")
     * )
     */
    public function show($id)
    {
        $item = ExamSchedule::with(['class', 'section', 'subject', 'teacher', 'exam'])->findOrFail($id);
        return response()->json([
            'success' => true,
            'data'    => $item
        ]);
    }

    /**
     * @OA\Put(
     *      path="/api/exam-schedules/{id}",
     *      operationId="updateExamSchedule",
     *      tags={"6. Examinations & Assessment - Exam Schedule"},
     *      summary="Update existing exam schedule",
     *      security={{"sanctum":{}}},
     *      @OA\Parameter(name="id", required=true, in="path", @OA\Schema(type="integer")),
     *      @OA\Response(response=200, description="Updated successfully")
     * )
     */
    public function update(Request $request, $id)
    {
        $item = ExamSchedule::findOrFail($id);
        $item->update($request->only([
            'exam_id', 'class_id', 'section_id', 'subject_id', 'teacher_id',
            'exam_date', 'start_time', 'end_time', 'duration', 'room'
        ]));

        $item->load(['class', 'section', 'subject', 'teacher', 'exam']);

        return response()->json([
            'success' => true,
            'message' => 'Exam Schedule updated successfully',
            'data'    => $item
        ]);
    }

    /**
     * @OA\Delete(
     *      path="/api/exam-schedules/{id}",
     *      operationId="deleteExamSchedule",
     *      tags={"6. Examinations & Assessment - Exam Schedule"},
     *      summary="Delete exam schedule",
     *      security={{"sanctum":{}}},
     *      @OA\Parameter(name="id", required=true, in="path", @OA\Schema(type="integer")),
     *      @OA\Response(response=200, description="Deleted successfully")
     * )
     */
    public function destroy($id)
    {
        $item = ExamSchedule::findOrFail($id);
        $item->delete();

        return response()->json([
            'success' => true,
            'message' => 'Exam Schedule deleted successfully'
        ]);
    }
}
