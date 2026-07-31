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
 *                  @OA\Property(property="card_stats", type="object"),
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
        $currentMonth = $now->month;
        $currentYear = $now->year;

        // General Settings / School Info
        $settings = \App\Models\GeneralSetting::first();
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

        $cardStats = [
            'students' => [
                'total_count' => $summary['students'],
                'new_this_month' => $newStudentsThisMonth,
                'pending_fee_payments' => $studentsPendingFeesCount,
            ],
            'teachers' => [
                'total_count' => $summary['teachers'],
                'new_this_month' => $newTeachersThisMonth,
                'pending_fee_payments' => 0,
            ],
            'parents' => [
                'total_count' => $summary['parents'],
                'new_this_month' => $newParentsThisMonth,
                'pending_fee_payments' => 0,
            ],
            'staffs' => [
                'total_count' => $summary['staffs'],
                'new_this_month' => $newStaffsThisMonth,
                'pending_fee_payments' => 0,
            ],
        ];

        // Pending Fee Payments
        $pendingFees = StudentFee::where('status', 'pending')->orWhere('status', 'overdue');

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

        // Yearly Income and Expenses (for Chart)
        $yearlyStats = [];
        for ($i = 1; $i <= 12; $i++) {
            $monthName = Carbon::create()->month($i)->format('M');
            
            $income = Payment::whereMonth('payment_date', $i)
                ->whereYear('payment_date', $currentYear)
                ->sum('amount');
                
            $expenses = Expense::whereMonth('date', $i)
                ->whereYear('date', $currentYear)
                ->sum('amount');

            $yearlyStats[] = [
                'month' => $monthName,
                'income' => (float)$income,
                'expenses' => (float)$expenses,
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

        // Roles for "Add Event" modal
        $roles = DB::table('roles')->pluck('name')->toArray();

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
