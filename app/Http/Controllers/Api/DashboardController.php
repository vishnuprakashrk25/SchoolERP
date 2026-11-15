<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\Staff;
use App\Models\StudentParent;
use App\Models\Payment;
use App\Models\Expense;
use App\Models\Notification;
use App\Models\Todo;
use App\Models\Event;
use App\Models\StudentFee;
use App\Models\StudentAttendance;
use App\Models\StaffAttendance;
use App\Models\Classes;
use App\Models\HomeworkSubmission;
use App\Models\Complaint;
use App\Models\PhoneCallLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * @OA\Get(
     *      path="/api/dashboard",
     *      operationId="getDashboardData",
     *      tags={"Dashboard"},
     *      summary="Get dashboard statistics and data",
     *      description="Returns summary counts, monthly/yearly stats, notices, todos, events, and roles for the dashboard.",
     *      security={{"sanctum":{}}},
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(
     *              @OA\Property(property="success", type="boolean", example=true),
     *              @OA\Property(
     *                  property="data",
     *                  type="object",
     *                  @OA\Property(property="school_info", type="object"),
     *                  @OA\Property(
     *                      property="card_stats",
     *                      type="object",
     *                      description="Entity cards with dynamic rotating notification messages",
     *                      @OA\Property(
     *                          property="students",
     *                          type="object",
     *                          @OA\Property(property="total_count", type="integer", example=30),
     *                          @OA\Property(property="new_this_month", type="integer", example=4),
     *                          @OA\Property(property="pending_fee_payments", type="integer", example=2),
     *                          @OA\Property(property="notification_message", type="string", example="Today's student attendance pending"),
     *                          @OA\Property(
     *                              property="messages",
     *                              type="array",
     *                              @OA\Items(
     *                                  @OA\Property(property="text", type="string", example="2 pending fee payments"),
     *                                  @OA\Property(property="category", type="string", example="payment"),
     *                                  @OA\Property(property="type", type="string", example="warning"),
     *                                  @OA\Property(property="count", type="integer", example=2),
     *                                  @OA\Property(property="link", type="string", example="/admin/fees")
     *                              )
     *                          )
     *                      ),
     *                      @OA\Property(
     *                          property="teachers",
     *                          type="object",
     *                          @OA\Property(property="total_count", type="integer", example=5),
     *                          @OA\Property(property="new_this_month", type="integer", example=4),
     *                          @OA\Property(property="pending_fee_payments", type="integer", example=0),
     *                          @OA\Property(property="notification_message", type="string", example="All teachers present today"),
     *                          @OA\Property(
     *                              property="messages",
     *                              type="array",
     *                              @OA\Items(
     *                                  @OA\Property(property="text", type="string", example="All teachers present today"),
     *                                  @OA\Property(property="category", type="string", example="attendance"),
     *                                  @OA\Property(property="type", type="string", example="success"),
     *                                  @OA\Property(property="count", type="integer", example=0),
     *                                  @OA\Property(property="link", type="string", example="/admin/staff-attendance")
     *                              )
     *                          )
     *                      ),
     *                      @OA\Property(
     *                          property="parents",
     *                          type="object",
     *                          @OA\Property(property="total_count", type="integer", example=30),
     *                          @OA\Property(property="new_this_month", type="integer", example=4),
     *                          @OA\Property(property="pending_fee_payments", type="integer", example=2),
     *                          @OA\Property(property="notification_message", type="string", example="2 pending fee reminders"),
     *                          @OA\Property(
     *                              property="messages",
     *                              type="array",
     *                              @OA\Items(
     *                                  @OA\Property(property="text", type="string", example="2 pending fee reminders"),
     *                                  @OA\Property(property="category", type="string", example="payment"),
     *                                  @OA\Property(property="type", type="string", example="warning"),
     *                                  @OA\Property(property="count", type="integer", example=2),
     *                                  @OA\Property(property="link", type="string", example="/admin/fees")
     *                              )
     *                          )
     *                      ),
     *                      @OA\Property(
     *                          property="staffs",
     *                          type="object",
     *                          @OA\Property(property="total_count", type="integer", example=8),
     *                          @OA\Property(property="new_this_month", type="integer", example=4),
     *                          @OA\Property(property="pending_fee_payments", type="integer", example=0),
     *                          @OA\Property(property="notification_message", type="string", example="Today's staff attendance pending"),
     *                          @OA\Property(
     *                              property="messages",
     *                              type="array",
     *                              @OA\Items(
     *                                  @OA\Property(property="text", type="string", example="Today's staff attendance pending"),
     *                                  @OA\Property(property="category", type="string", example="attendance"),
     *                                  @OA\Property(property="type", type="string", example="info"),
     *                                  @OA\Property(property="count", type="integer", example=0),
     *                                  @OA\Property(property="link", type="string", example="/admin/staff-attendance")
     *                              )
     *                          )
     *                      )
     *                  ),
     *                  @OA\Property(property="summary", type="object"),
     *                  @OA\Property(property="monthly_stats", type="object"),
     *                  @OA\Property(property="yearly_stats", type="array", @OA\Items(type="object")),
     *                  @OA\Property(
     *                      property="notifications",
     *                      type="object",
     *                      @OA\Property(
     *                          property="pending_fee_payments",
     *                          type="object",
     *                          @OA\Property(property="total_count", type="integer", example=5),
     *                          @OA\Property(property="total_amount", type="number", example=2500.00),
     *                          @OA\Property(property="overdue_count", type="integer", example=2),
     *                          @OA\Property(property="message", type="string", example="5 pending fee payments"),
     *                          @OA\Property(property="recent_records", type="array", @OA\Items(type="object"))
     *                      )
     *                  ),
     *                  @OA\Property(property="notices", type="array", @OA\Items(type="object")),
     *                  @OA\Property(property="todos", type="array", @OA\Items(type="object")),
     *                  @OA\Property(property="events", type="array", @OA\Items(type="object")),
     *                  @OA\Property(property="roles", type="array", @OA\Items(type="string")),
     *                  @OA\Property(property="current_month_year", type="string"),
     *                  @OA\Property(property="current_year", type="integer")
     *              )
     *          )
     *      ),
     *      @OA\Response(response=401, description="Unauthenticated")
     * )
     */
    public function index(Request $request)
    {
        $now = Carbon::now();
        $today = $now->toDateString();
        $currentMonth = $now->month;
        $currentYear = $now->year;

        // General Settings / School Info
        $settings = \App\Models\GeneralSetting::cached();
        $schoolInfo = [
            'school_name' => $settings->school_name ?? 'School Name',
            'suic_code' => $settings->suic_code ?? null,
            'zone' => $settings->zone ?? null,
            'category_of_institution' => $settings->category_of_institution ?? null,
        ];

        // Summary Counts
        $summary = [
            'students' => Student::count(),
            'teachers' => Teacher::count(),
            'parents'  => StudentParent::count(),
            'staffs'   => Staff::count(),
        ];

        // Detailed Card Stats (New this month, Pending fee payments)
        $newStudentsThisMonth = Student::whereMonth('created_at', $currentMonth)->whereYear('created_at', $currentYear)->count();
        $newTeachersThisMonth = Teacher::whereMonth('created_at', $currentMonth)->whereYear('created_at', $currentYear)->count();
        $newParentsThisMonth  = StudentParent::whereMonth('created_at', $currentMonth)->whereYear('created_at', $currentYear)->count();
        $newStaffsThisMonth   = Staff::whereMonth('created_at', $currentMonth)->whereYear('created_at', $currentYear)->count();

        $studentsPendingFeesCount = StudentFee::whereIn('status', ['pending', 'overdue'])
            ->whereHas('student')
            ->distinct('student_id')
            ->count('student_id');

        // ==========================================
        // 1. STUDENTS CARD NOTIFICATION MESSAGES
        // ==========================================
        $studentMessages = [];

        // Attendance message
        $studentAttendanceTodayCount = StudentAttendance::whereDate('date', $today)->count();
        if ($studentAttendanceTodayCount > 0) {
            $studentsAbsentToday = StudentAttendance::whereDate('date', $today)
                ->where(function ($q) {
                    $q->whereIn('status', ['absent', 'Absent', 'A']);
                })->count();

            if ($studentsAbsentToday > 0) {
                $studentMessages[] = [
                    'text' => "{$studentsAbsentToday} student" . ($studentsAbsentToday !== 1 ? 's' : '') . " absent today",
                    'category' => 'attendance',
                    'type' => 'danger',
                    'count' => $studentsAbsentToday,
                    'link' => '/admin/student-attendance'
                ];
            } else {
                $studentMessages[] = [
                    'text' => "100% student attendance today",
                    'category' => 'attendance',
                    'type' => 'success',
                    'count' => 0,
                    'link' => '/admin/student-attendance'
                ];
            }
        } else {
            $studentMessages[] = [
                'text' => "Today's student attendance pending",
                'category' => 'attendance',
                'type' => 'info',
                'count' => 0,
                'link' => '/admin/student-attendance'
            ];
        }

        // Pending Fees message
        if ($studentsPendingFeesCount > 0) {
            $studentMessages[] = [
                'text' => "{$studentsPendingFeesCount} pending fee payment" . ($studentsPendingFeesCount !== 1 ? 's' : ''),
                'category' => 'payment',
                'type' => 'warning',
                'count' => $studentsPendingFeesCount,
                'link' => '/admin/fees'
            ];
        } else {
            $studentMessages[] = [
                'text' => "No pending fee payments",
                'category' => 'payment',
                'type' => 'success',
                'count' => 0,
                'link' => '/admin/fees'
            ];
        }

        // Unassigned students message
        $unassignedStudentsCount = Student::where(function ($q) {
            $q->whereNull('class_id')->orWhereNull('section_id');
        })->count();
        if ($unassignedStudentsCount > 0) {
            $studentMessages[] = [
                'text' => "{$unassignedStudentsCount} unassigned student" . ($unassignedStudentsCount !== 1 ? 's' : ''),
                'category' => 'academic',
                'type' => 'info',
                'count' => $unassignedStudentsCount,
                'link' => '/admin/students?filter=unassigned'
            ];
        }

        // ==========================================
        // 2. TEACHERS CARD NOTIFICATION MESSAGES
        // ==========================================
        $teacherMessages = [];

        // Teacher Attendance / Leave message
        $teachersRecordedToday = StaffAttendance::whereDate('date', $today)
            ->whereHas('staff', function ($q) {
                $q->where('role', 'Teacher')->orWhere('role', 'teacher');
            })->count();

        if ($teachersRecordedToday > 0) {
            $teachersAbsentToday = StaffAttendance::whereDate('date', $today)
                ->whereHas('staff', function ($q) {
                    $q->where('role', 'Teacher')->orWhere('role', 'teacher');
                })
                ->where(function ($q) {
                    $q->whereIn('status', ['absent', 'Absent', 'on_leave', 'Leave', 'leave', 'On Leave']);
                })->count();

            if ($teachersAbsentToday > 0) {
                $teacherMessages[] = [
                    'text' => "{$teachersAbsentToday} teacher" . ($teachersAbsentToday !== 1 ? 's' : '') . " absent/on leave today",
                    'category' => 'attendance',
                    'type' => 'danger',
                    'count' => $teachersAbsentToday,
                    'link' => '/admin/staff-attendance'
                ];
            } else {
                $teacherMessages[] = [
                    'text' => "All teachers present today",
                    'category' => 'attendance',
                    'type' => 'success',
                    'count' => 0,
                    'link' => '/admin/staff-attendance'
                ];
            }
        } else {
            $teacherMessages[] = [
                'text' => "Today's teacher attendance pending",
                'category' => 'attendance',
                'type' => 'info',
                'count' => 0,
                'link' => '/admin/staff-attendance'
            ];
        }

        // Classes without assigned Class Teacher
        $classesWithoutTeacher = Classes::whereDoesntHave('classTeachers')->count();
        if ($classesWithoutTeacher > 0) {
            $teacherMessages[] = [
                'text' => "{$classesWithoutTeacher} class" . ($classesWithoutTeacher !== 1 ? 'es' : '') . " without assigned teacher",
                'category' => 'academic',
                'type' => 'warning',
                'count' => $classesWithoutTeacher,
                'link' => '/admin/class-teachers'
            ];
        }

        // Pending homework submissions to evaluate
        $pendingEvaluations = HomeworkSubmission::whereIn('status', ['submitted', 'pending'])->count();
        if ($pendingEvaluations > 0) {
            $teacherMessages[] = [
                'text' => "{$pendingEvaluations} homework submission" . ($pendingEvaluations !== 1 ? 's' : '') . " to evaluate",
                'category' => 'academic',
                'type' => 'info',
                'count' => $pendingEvaluations,
                'link' => '/admin/homework-evaluation'
            ];
        } else {
            $teacherMessages[] = [
                'text' => "All homework evaluated",
                'category' => 'academic',
                'type' => 'success',
                'count' => 0,
                'link' => '/admin/homework'
            ];
        }

        // ==========================================
        // 3. PARENTS CARD NOTIFICATION MESSAGES
        // ==========================================
        $parentMessages = [];

        // Parent fee reminders
        if ($studentsPendingFeesCount > 0) {
            $parentMessages[] = [
                'text' => "{$studentsPendingFeesCount} pending fee reminder" . ($studentsPendingFeesCount !== 1 ? 's' : ''),
                'category' => 'payment',
                'type' => 'warning',
                'count' => $studentsPendingFeesCount,
                'link' => '/admin/fees'
            ];
        } else {
            $parentMessages[] = [
                'text' => "No pending fee reminders",
                'category' => 'payment',
                'type' => 'success',
                'count' => 0,
                'link' => '/admin/fees'
            ];
        }

        // Pending parent complaints / inquiries
        $pendingComplaints = Complaint::whereIn('status', ['pending', 'open', 'in_progress'])->count();
        if ($pendingComplaints > 0) {
            $parentMessages[] = [
                'text' => "{$pendingComplaints} pending parent complaint" . ($pendingComplaints !== 1 ? 's' : ''),
                'category' => 'support',
                'type' => 'warning',
                'count' => $pendingComplaints,
                'link' => '/admin/complaints'
            ];
        } else {
            $parentMessages[] = [
                'text' => "All complaints resolved",
                'category' => 'support',
                'type' => 'success',
                'count' => 0,
                'link' => '/admin/complaints'
            ];
        }

        // Parents missing phone numbers
        $parentsMissingContact = StudentParent::where(function ($q) {
            $q->whereNull('father_phone')->orWhere('father_phone', '');
        })->where(function ($q) {
            $q->whereNull('mother_phone')->orWhere('mother_phone', '');
        })->where(function ($q) {
            $q->whereNull('guardian_phone')->orWhere('guardian_phone', '');
        })->count();

        if ($parentsMissingContact > 0) {
            $parentMessages[] = [
                'text' => "{$parentsMissingContact} parent" . ($parentsMissingContact !== 1 ? 's' : '') . " missing contact phone",
                'category' => 'general',
                'type' => 'info',
                'count' => $parentsMissingContact,
                'link' => '/admin/parents'
            ];
        }

        // ==========================================
        // 4. STAFFS CARD NOTIFICATION MESSAGES
        // ==========================================
        $staffMessages = [];

        // Staff attendance
        $staffRecordedToday = StaffAttendance::whereDate('date', $today)
            ->whereHas('staff', function ($q) {
                $q->where('role', '!=', 'Teacher')->where('role', '!=', 'teacher');
            })->count();

        if ($staffRecordedToday > 0) {
            $staffAbsentToday = StaffAttendance::whereDate('date', $today)
                ->whereHas('staff', function ($q) {
                    $q->where('role', '!=', 'Teacher')->where('role', '!=', 'teacher');
                })
                ->where(function ($q) {
                    $q->whereIn('status', ['absent', 'Absent', 'on_leave', 'Leave', 'leave', 'On Leave']);
                })->count();

            if ($staffAbsentToday > 0) {
                $staffMessages[] = [
                    'text' => "{$staffAbsentToday} staff member" . ($staffAbsentToday !== 1 ? 's' : '') . " absent/on leave today",
                    'category' => 'attendance',
                    'type' => 'danger',
                    'count' => $staffAbsentToday,
                    'link' => '/admin/staff-attendance'
                ];
            } else {
                $staffMessages[] = [
                    'text' => "All staff members present today",
                    'category' => 'attendance',
                    'type' => 'success',
                    'count' => 0,
                    'link' => '/admin/staff-attendance'
                ];
            }
        } else {
            $staffMessages[] = [
                'text' => "Today's staff attendance pending",
                'category' => 'attendance',
                'type' => 'info',
                'count' => 0,
                'link' => '/admin/staff-attendance'
            ];
        }

        // Administrative tasks / todos
        $pendingTodos = Todo::where('is_completed', false)->count();
        if ($pendingTodos > 0) {
            $staffMessages[] = [
                'text' => "{$pendingTodos} pending administrative task" . ($pendingTodos !== 1 ? 's' : ''),
                'category' => 'tasks',
                'type' => 'warning',
                'count' => $pendingTodos,
                'link' => '/admin/todos'
            ];
        } else {
            $staffMessages[] = [
                'text' => "All administrative tasks completed",
                'category' => 'tasks',
                'type' => 'success',
                'count' => 0,
                'link' => '/admin/todos'
            ];
        }

        // Recent front-office calls
        $recentCalls = PhoneCallLog::whereDate('created_at', '>=', Carbon::now()->subDays(7)->toDateString())->count();
        if ($recentCalls > 0) {
            $staffMessages[] = [
                'text' => "{$recentCalls} front-office call log" . ($recentCalls !== 1 ? 's' : '') . " this week",
                'category' => 'general',
                'type' => 'info',
                'count' => $recentCalls,
                'link' => '/admin/phone-call-log'
            ];
        }

        // Compile Card Stats
        $cardStats = [
            'students' => [
                'total_count' => $summary['students'],
                'new_this_month' => $newStudentsThisMonth,
                'pending_fee_payments' => $studentsPendingFeesCount,
                'notification_message' => $studentMessages[0]['text'] ?? null,
                'messages' => $studentMessages,
            ],
            'teachers' => [
                'total_count' => $summary['teachers'],
                'new_this_month' => $newTeachersThisMonth,
                'pending_fee_payments' => 0,
                'notification_message' => $teacherMessages[0]['text'] ?? null,
                'messages' => $teacherMessages,
            ],
            'parents' => [
                'total_count' => $summary['parents'],
                'new_this_month' => $newParentsThisMonth,
                'pending_fee_payments' => $studentsPendingFeesCount,
                'notification_message' => $parentMessages[0]['text'] ?? null,
                'messages' => $parentMessages,
            ],
            'staffs' => [
                'total_count' => $summary['staffs'],
                'new_this_month' => $newStaffsThisMonth,
                'pending_fee_payments' => 0,
                'notification_message' => $staffMessages[0]['text'] ?? null,
                'messages' => $staffMessages,
            ],
        ];

        // Pending Fee Payments
        $pendingFees = StudentFee::whereIn('status', ['pending', 'overdue']);

        // Total pending fees count
        $totalPendingFees = (clone $pendingFees)->count();
        $totalPendingAmount = (clone $pendingFees)->sum('amount');

        // Pending fees linked to each entity type
        $pendingFeesByStudents = (clone $pendingFees)
            ->whereHas('student')
            ->count();

        $pendingFeesByTeachers = (clone $pendingFees)
            ->whereHas('student.studentAcademics', function ($q) {
                // Students who are children of a teacher — approximation
            })
            ->count();

        // Recent pending fee records (for notification list)
        $recentPendingFees = StudentFee::with(['student:id,first_name,last_name,admission_no'])
            ->whereIn('status', ['pending', 'overdue'])
            ->orderBy('due_date', 'asc')
            ->limit(10)
            ->get(['id', 'student_id', 'amount', 'paid_amount', 'status', 'due_date']);

        // Overdue count (due_date passed and not paid)
        $overdueCount = StudentFee::where('status', 'pending')
            ->where('due_date', '<', $now->toDateString())
            ->count();

        // Monthly Income and Expenses
        $monthlyIncome = Payment::whereMonth('payment_date', $currentMonth)
            ->whereYear('payment_date', $currentYear)
            ->sum('amount');

        $monthlyExpenses = Expense::whereMonth('date', $currentMonth)
            ->whereYear('date', $currentYear)
            ->sum('amount');

        $monthlyStats = [
            'total_income' => (float)$monthlyIncome,
            'total_expenses' => (float)$monthlyExpenses,
            'total_profit' => (float)($monthlyIncome - $monthlyExpenses),
            'total_revenue' => (float)$monthlyIncome,
            'wallet_balance' => (float)($monthlyIncome - $monthlyExpenses), // Simplified
        ];

        // Yearly Income and Expenses (for Chart) - Optimized to 2 grouped queries
        $incomeByMonth = Payment::whereYear('payment_date', $currentYear)
            ->selectRaw('MONTH(payment_date) as m, SUM(amount) as total')
            ->groupBy('m')
            ->pluck('total', 'm');

        $expensesByMonth = Expense::whereYear('date', $currentYear)
            ->selectRaw('MONTH(date) as m, SUM(amount) as total')
            ->groupBy('m')
            ->pluck('total', 'm');

        $yearlyStats = [];
        for ($i = 1; $i <= 12; $i++) {
            $monthName = Carbon::create()->month($i)->format('M');
            $yearlyStats[] = [
                'month'    => $monthName,
                'income'   => (float)($incomeByMonth[$i] ?? 0),
                'expenses' => (float)($expensesByMonth[$i] ?? 0),
            ];
        }

        // Notice Board
        $notices = Notification::latest()
            ->limit(10)
            ->get(['id', 'title', 'message', 'created_at']);

        // To Do List
        $todos = Todo::where('user_id', $request->user()->id ?? null)
            ->orderBy('is_completed', 'asc')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Calendar Events
        $events = Event::whereYear('start_date', $currentYear)
            ->get();

        // Roles for "Add Event" modal - deduplicated across guards
        $roles = array_values(array_unique(DB::table('roles')->pluck('name')->toArray()));

        return response()->json([
            'success' => true,
            'data' => [
                'school_info'  => $schoolInfo,
                'summary'      => $summary,
                'card_stats'   => $cardStats,
                'monthly_stats' => $monthlyStats,
                'yearly_stats' => $yearlyStats,
                'notifications' => [
                    'pending_fee_payments' => [
                        'total_count'    => $totalPendingFees,
                        'total_amount'   => (float) $totalPendingAmount,
                        'overdue_count'  => $overdueCount,
                        'message'        => $totalPendingFees . ' pending fee payment' . ($totalPendingFees !== 1 ? 's' : ''),
                        'recent_records' => $recentPendingFees,
                    ],
                ],
                'notices'           => $notices,
                'todos'             => $todos,
                'events'            => $events,
                'roles'             => $roles,
                'current_month_year' => $now->format('F Y'),
                'current_year'      => $currentYear,
            ]
        ]);
    }
}
