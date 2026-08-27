<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\Staff;
use App\Models\StudentParent;
use App\Models\Teacher;
use App\Models\ClassSubject;
use App\Models\Subject;
use Illuminate\Support\Str;

class GlobalSearchController extends Controller
{
    /**
     * @OA\Get(
     *      path="/api/search",
     *      operationId="globalSearch",
     *      tags={"Utility"},
     *      summary="Global Search across ERP modules",
     *      description="Searches Students, Teachers, Parents, and Staff by keyword with formatting matching the header search mockup.",
     *      security={{"sanctum":{}}},
     *      @OA\Parameter(
     *          name="q",
     *          description="Search keyword",
     *          required=true,
     *          in="query",
     *          @OA\Schema(type="string")
     *      ),
     *      @OA\Parameter(
     *          name="types",
     *          description="Filter search types (comma-separated or array: Student, Teacher, Parents, Staffs)",
     *          required=false,
     *          in="query",
     *          @OA\Schema(
     *              type="array",
     *              @OA\Items(type="string")
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation"
     *      )
     * )
     */
    public function search(Request $request)
    {
        $keyword = $request->query('q');
        $typesInput = $request->query('types');

        if (is_string($typesInput)) {
            $typesInput = explode(',', $typesInput);
        }
        $types = $typesInput ? array_map('trim', $typesInput) : [];

        if (!$keyword) {
            return response()->json([
                'success' => true,
                'data' => []
            ]);
        }

        $term = "%{$keyword}%";
        $results = [];

        // 1. STUDENTS
        if (empty($types) || in_array('Student', $types)) {
            $students = Student::with(['class', 'section'])
                ->where(function($query) use ($term) {
                    $query->where('first_name', 'LIKE', $term)
                          ->orWhere('last_name', 'LIKE', $term)
                          ->orWhere('full_name', 'LIKE', $term)
                          ->orWhere('admission_no', 'LIKE', $term)
                          ->orWhere('email', 'LIKE', $term)
                          ->orWhere('phone', 'LIKE', $term);
                })
                ->take(15)
                ->get();

            foreach ($students as $stu) {
                $className = $stu->class ? $stu->class->name : '';
                $sectionName = $stu->section ? $stu->section->name : '';
                $classDetail = trim($className . $sectionName);
                $classDetailStr = $classDetail ? "Class {$classDetail}" : 'Student';
                $admNoStr = $stu->admission_no ? " • Adm No:{$stu->admission_no}" : '';
                
                $results[] = [
                    'id' => $stu->id,
                    'student_id' => $stu->id,
                    'name' => $stu->full_name,
                    'type' => 'Student',
                    'details' => $classDetailStr . $admNoStr,
                    'photo' => $stu->photo ? url($stu->photo) : null,
                ];
            }
        }

        // 2. TEACHERS
        if (empty($types) || in_array('Teacher', $types)) {
            $teachers = Teacher::with(['classTeachers.class', 'classTeachers.section', 'classSubjects.subject'])
                ->where(function($query) use ($term) {
                    $query->where('first_name', 'LIKE', $term)
                          ->orWhere('last_name', 'LIKE', $term)
                          ->orWhere('full_name', 'LIKE', $term)
                          ->orWhere('staff_code', 'LIKE', $term)
                          ->orWhere('email', 'LIKE', $term)
                          ->orWhere('phone', 'LIKE', $term);
                })
                ->take(15)
                ->get();

            foreach ($teachers as $t) {
                $ctInfo = '';
                $ct = $t->classTeachers->first();
                if ($ct && $ct->class) {
                    $cName = $ct->class->name;
                    $sName = $ct->section ? $ct->section->name : '';
                    $ctInfo = "Class Teacher ({$cName}{$sName})";
                }
                
                $subInfo = '';
                $sub = $t->classSubjects->first();
                if ($sub && $sub->subject) {
                    $subInfo = "Subject: {$sub->subject->name}";
                }
                
                $details = [];
                if ($ctInfo) {
                    $details[] = $ctInfo;
                }
                if ($subInfo) {
                    $details[] = $subInfo;
                }
                
                $detailStr = !empty($details) ? implode(' • ', $details) : 'Teacher';
                
                $results[] = [
                    'id' => $t->id,
                    'teacher_id' => $t->id,
                    'name' => $t->full_name,
                    'type' => 'Teacher',
                    'details' => $detailStr,
                    'photo' => $t->photo ? url($t->photo) : null,
                ];
            }
        }

        // 3. PARENTS
        if (empty($types) || in_array('Parents', $types)) {
            $parents = StudentParent::with(['student.class', 'student.section'])
                ->where(function($query) use ($term) {
                    $query->where('father_name', 'LIKE', $term)
                          ->orWhere('mother_name', 'LIKE', $term)
                          ->orWhere('guardian_name', 'LIKE', $term)
                          ->orWhere('father_phone', 'LIKE', $term)
                          ->orWhere('mother_phone', 'LIKE', $term)
                          ->orWhere('guardian_phone', 'LIKE', $term);
                })
                ->take(15)
                ->get();

            foreach ($parents as $p) {
                $name = '';
                if ($p->father_name && Str::contains(strtolower($p->father_name), strtolower($keyword))) {
                    $name = $p->father_name;
                } elseif ($p->mother_name && Str::contains(strtolower($p->mother_name), strtolower($keyword))) {
                    $name = $p->mother_name;
                } elseif ($p->guardian_name && Str::contains(strtolower($p->guardian_name), strtolower($keyword))) {
                    $name = $p->guardian_name;
                } else {
                    $name = $p->father_name ?: ($p->mother_name ?: ($p->guardian_name ?: 'Parent'));
                }
                
                $stuInfo = '';
                if ($p->student) {
                    $sFirstName = $p->student->full_name ?: $p->student->first_name;
                    $className = $p->student->class ? $p->student->class->name : '';
                    $sectionName = $p->student->section ? $p->student->section->name : '';
                    $classDetail = trim($className . $sectionName);
                    $stuInfo = "Parent of {$sFirstName}" . ($classDetail ? " ({$classDetail})" : '');
                } else {
                    $stuInfo = 'Parent';
                }
                
                $results[] = [
                    'id' => $p->id,
                    'parent_id' => $p->id,
                    'student_parent_id' => $p->id,
                    'name' => $name,
                    'type' => 'Parents',
                    'details' => $stuInfo,
                    'photo' => $p->father_photo ? url($p->father_photo) : ($p->mother_photo ? url($p->mother_photo) : ($p->guardian_photo ? url($p->guardian_photo) : null)),
                ];
            }
        }

        // 4. STAFFS
        if (empty($types) || in_array('Staffs', $types)) {
            $staffMembers = Staff::where(function($query) {
                    $query->whereNull('role')
                          ->orWhereNotIn('role', ['Teacher', 'teacher']);
                })
                ->where(function($query) use ($term) {
                    $query->where('first_name', 'LIKE', $term)
                          ->orWhere('last_name', 'LIKE', $term)
                          ->orWhere('full_name', 'LIKE', $term)
                          ->orWhere('staff_code', 'LIKE', $term)
                          ->orWhere('email', 'LIKE', $term)
                          ->orWhere('phone', 'LIKE', $term);
                })
                ->take(15)
                ->get();

            foreach ($staffMembers as $s) {
                $desig = $s->designation;
                if ($desig) {
                    $desigStr = Str::contains(strtolower($desig), 'team') ? $desig : "{$desig} Team";
                } else {
                    $desigStr = 'Staff';
                }
                
                $results[] = [
                    'id' => $s->id,
                    'staff_id' => $s->id,
                    'name' => $s->full_name,
                    'type' => 'Staffs',
                    'details' => $desigStr,
                    'photo' => $s->photo ? url($s->photo) : null,
                ];
            }
        }

        return response()->json([
            'success' => true,
            'data' => $results
        ]);
    }
}
