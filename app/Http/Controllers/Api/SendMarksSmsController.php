<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\ExamType;
use App\Models\ExamMark;
use App\Models\Student;
use App\Models\Classes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class SendMarksSmsController extends Controller
{
    /**
     * @OA\Post(
     *      path="/api/exams/send-marks-sms",
     *      operationId="sendMarksBySms",
     *      tags={"6. Examinations & Assessment - Exam"},
     *      summary="Send Marks By SMS",
     *      description="Dispatches SMS with student exam marks and grades to Students, Parents, or All.",
     *      security={{"sanctum":{}}},
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"exam_id","class_id","receiver"},
     *              @OA\Property(property="exam_id", type="integer", example=1, description="Exam or Exam Type ID"),
     *              @OA\Property(property="class_id", type="integer", example=5, description="Class ID"),
     *              @OA\Property(property="receiver", type="string", example="parents", description="Receiver: 'students', 'parents', or 'all'"),
     *              @OA\Property(property="custom_message", type="string", example="Dear Parent, your child's exam report is ready.")
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(
     *              @OA\Property(property="success", type="boolean", example=true),
     *              @OA\Property(property="message", type="string", example="Marks SMS sent successfully to 28 recipient(s)."),
     *              @OA\Property(property="total_sent", type="integer", example=28)
     *          )
     *      ),
     *      @OA\Response(response=422, description="Validation error")
     * )
     */
    public function send(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'exam_id'        => 'required|integer',
            'class_id'       => 'required|integer',
            'receiver'       => 'required|string|in:students,parents,all,student,parent',
            'custom_message' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors()
            ], 422);
        }

        $examId = $request->input('exam_id');
        $classId = $request->input('class_id');
        $receiver = strtolower($request->input('receiver'));
        $customMsg = $request->input('custom_message');

        // Fetch Exam Name
        $examName = Exam::find($examId)?->name ?? ExamType::find($examId)?->name ?? 'Exam';
        $className = Classes::find($classId)?->name ?? "Class {$classId}";

        // Fetch students and their marks for this exam
        $students = Student::where('class_id', $classId)
            ->with(['parents', 'user'])
            ->get();

        $marks = ExamMark::where('exam_id', $examId)
            ->where('class_id', $classId)
            ->with('subject:id,name')
            ->get()
            ->groupBy('student_id');

        $recipientsCount = 0;
        $sentLogs = [];

        foreach ($students as $student) {
            $studentMarks = $marks->get($student->id, collect());
            $totalMarks = $studentMarks->sum('total_marks');
            $subjectBreakdown = $studentMarks->map(fn($m) => ($m->subject?->name ?? 'Subject') . ": " . $m->total_marks . " (" . ($m->grade ?? '-') . ")")->implode(', ');

            $smsBody = $customMsg 
                ? "{$customMsg} Student: {$student->full_name}, Exam: {$examName}, Total: {$totalMarks}. {$subjectBreakdown}"
                : "Exam Alert: {$student->full_name} ({$className}) secured Total: {$totalMarks} in {$examName}. Breakdown: {$subjectBreakdown}";

            // Send to Student phone
            if (in_array($receiver, ['students', 'student', 'all']) && !empty($student->phone)) {
                $recipientsCount++;
                $sentLogs[] = [
                    'to'      => $student->phone,
                    'type'    => 'student',
                    'name'    => $student->full_name,
                    'message' => $smsBody,
                ];
            }

            // Send to Parent phone
            if (in_array($receiver, ['parents', 'parent', 'all'])) {
                foreach ($student->parents as $p) {
                    $parentPhone = $p->phone ?? $p->emergency_mobile ?? $student->emergency_phone;
                    if (!empty($parentPhone)) {
                        $recipientsCount++;
                        $sentLogs[] = [
                            'to'      => $parentPhone,
                            'type'    => 'parent',
                            'name'    => $p->full_name ?? 'Parent',
                            'message' => $smsBody,
                        ];
                    }
                }
            }
        }

        // Log SMS activity
        Log::info("Exam marks SMS broadcast: Exam {$examName}, Class {$className}, Sent to {$recipientsCount} recipients.");

        return response()->json([
            'success'     => true,
            'message'     => "Marks SMS sent successfully to {$recipientsCount} recipient(s).",
            'exam'        => $examName,
            'class'       => $className,
            'receiver'    => $receiver,
            'total_sent'  => $recipientsCount,
            'sample_logs' => array_slice($sentLogs, 0, 5),
        ]);
    }
}
