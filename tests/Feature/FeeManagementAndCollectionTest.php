<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Classes;
use App\Models\Section;
use App\Models\Student;
use App\Models\AcademicYear;
use App\Models\FeeGroup;
use App\Models\FeeType;
use App\Models\FeeStructure;
use App\Models\StudentFee;
use App\Models\StudentFeeItem;
use App\Models\Payment;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class FeeManagementAndCollectionTest extends TestCase
{
    use DatabaseTransactions;

    protected $user;
    protected $academicYear;
    protected $class;
    protected $student;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::first() ?? User::factory()->create();
        $this->actingAs($this->user, 'sanctum');

        $this->academicYear = AcademicYear::firstOrCreate(
            ['name' => '2026-2027'],
            ['start_date' => '2026-04-01', 'end_date' => '2027-03-31']
        );

        $this->class = Classes::firstOrCreate(
            ['name' => 'Class 7 - Test'],
            ['academic_year_id' => $this->academicYear->id]
        );

        $this->student = Student::create([
            'admission_no' => 'ADM-FEE-' . uniqid(),
            'full_name' => 'John Fee Tester',
            'first_name' => 'John',
            'last_name' => 'Tester',
            'class_id' => $this->class->id,
            'academic_year_id' => $this->academicYear->id,
            'status' => 1,
        ]);
    }

    public function test_fee_group_crud()
    {
        $response = $this->postJson('/api/fee-groups', [
            'name' => 'Tuition Fees Test',
            'description' => 'Regular academic fee',
            'academic_year_id' => $this->academicYear->id,
            'is_active' => true,
        ]);

        $response->assertStatus(201);
        $response->assertJson(['success' => true]);
        $groupId = $response->json('data.id');

        $this->assertDatabaseHas('fee_groups', ['id' => $groupId, 'name' => 'Tuition Fees Test']);

        // List fee groups
        $listResponse = $this->getJson('/api/fee-groups?academic_year_id=' . $this->academicYear->id);
        $listResponse->assertStatus(200);
        $listResponse->assertJson(['success' => true]);
    }

    public function test_fee_type_creation_with_group()
    {
        $group = FeeGroup::create([
            'name' => 'Activity Fees',
            'academic_year_id' => $this->academicYear->id,
        ]);

        $response = $this->postJson('/api/fee-types', [
            'fee_group_id' => $group->id,
            'name' => 'Computer Lab Fee',
            'code' => 'COMP_01',
            'amount' => 450.00,
            'billing_cycle' => 'monthly',
        ]);

        $response->assertStatus(201);
        $response->assertJson(['success' => true]);
        $this->assertDatabaseHas('fee_types', [
            'fee_group_id' => $group->id,
            'name' => 'Computer Lab Fee',
            'code' => 'COMP_01',
            'amount' => 450.00,
        ]);
    }

    public function test_fee_structure_creation_with_items_and_installments()
    {
        $group = FeeGroup::create(['name' => 'Composite Fee Group']);
        $tuitType = FeeType::create(['fee_group_id' => $group->id, 'name' => 'Tuition', 'amount' => 1200]);
        $examType = FeeType::create(['fee_group_id' => $group->id, 'name' => 'Exam', 'amount' => 300]);

        $response = $this->postJson('/api/fee-structures', [
            'name' => 'Class 7 Annual Plan',
            'class_id' => $this->class->id,
            'academic_year_id' => $this->academicYear->id,
            'items' => [
                ['fee_type_id' => $tuitType->id, 'amount' => 1200.00, 'due_date' => '2026-05-10'],
                ['fee_type_id' => $examType->id, 'amount' => 300.00, 'due_date' => '2026-09-10'],
            ],
            'installments' => [
                ['name' => 'Term 1', 'due_date' => '2026-05-10', 'amount' => 750.00],
                ['name' => 'Term 2', 'due_date' => '2026-09-10', 'amount' => 750.00],
            ],
        ]);

        $response->assertStatus(201);
        $response->assertJson(['success' => true]);

        $structureId = $response->json('data.id');
        $this->assertDatabaseHas('fee_structures', [
            'id' => $structureId,
            'total_amount' => 1500.00,
        ]);
        $this->assertDatabaseHas('fee_structure_items', [
            'fee_structure_id' => $structureId,
            'fee_type_id' => $tuitType->id,
            'amount' => 1200.00,
        ]);
        $this->assertDatabaseHas('fee_installments', [
            'fee_structure_id' => $structureId,
            'name' => 'Term 1',
            'amount' => 750.00,
        ]);
    }

    public function test_student_fee_assignment_and_item_schedule_generation()
    {
        $group = FeeGroup::create(['name' => 'Standard Group']);
        $tuitType = FeeType::create(['fee_group_id' => $group->id, 'name' => 'Monthly Tuition', 'amount' => 1000]);
        $libType = FeeType::create(['fee_group_id' => $group->id, 'name' => 'Library', 'amount' => 200]);

        $structure = FeeStructure::create([
            'name' => 'Class 7 Plan',
            'class_id' => $this->class->id,
            'academic_year_id' => $this->academicYear->id,
            'total_amount' => 1200.00,
            'amount' => 1200.00,
        ]);
        $structure->items()->create(['fee_type_id' => $tuitType->id, 'amount' => 1000.00, 'due_date' => '2026-06-15']);
        $structure->items()->create(['fee_type_id' => $libType->id, 'amount' => 200.00, 'due_date' => '2026-06-15']);

        // Assign to student
        $assignResponse = $this->postJson('/api/student-fees/assign', [
            'fee_structure_id' => $structure->id,
            'student_ids' => [$this->student->id],
        ]);

        $assignResponse->assertStatus(200);
        $assignResponse->assertJson(['success' => true]);
        $this->assertEquals(1, $assignResponse->json('data.assigned_count'));

        // Verify StudentFee record and StudentFeeItem breakdown
        $studentFee = StudentFee::where('student_id', $this->student->id)->where('fee_structure_id', $structure->id)->first();
        $this->assertNotNull($studentFee);
        $this->assertEquals(1200.00, (float)$studentFee->amount);
        $this->assertEquals(1200.00, (float)$studentFee->balance_amount);
        $this->assertEquals('pending', $studentFee->status);

        $items = StudentFeeItem::where('student_fee_id', $studentFee->id)->get();
        $this->assertCount(2, $items);

        // Re-assigning should be idempotent (skipped)
        $reassignResponse = $this->postJson('/api/student-fees/assign', [
            'fee_structure_id' => $structure->id,
            'student_ids' => [$this->student->id],
        ]);
        $this->assertEquals(0, $reassignResponse->json('data.assigned_count'));
        $this->assertEquals(1, $reassignResponse->json('data.skipped_count'));
    }

    public function test_fee_collection_partial_payment_and_receipt_generation()
    {
        $group = FeeGroup::create(['name' => 'Primary Fee']);
        $feeType = FeeType::create(['fee_group_id' => $group->id, 'name' => 'Term Fee', 'amount' => 2000]);

        $structure = FeeStructure::create([
            'name' => 'Primary Fee Structure',
            'class_id' => $this->class->id,
            'academic_year_id' => $this->academicYear->id,
            'total_amount' => 2000.00,
        ]);
        $structure->items()->create(['fee_type_id' => $feeType->id, 'amount' => 2000.00]);

        $this->postJson('/api/student-fees/assign', [
            'fee_structure_id' => $structure->id,
            'student_ids' => [$this->student->id],
        ]);

        $studentFee = StudentFee::where('student_id', $this->student->id)->first();

        // 1. Collect partial payment: 800 out of 2000
        $collectResponse = $this->postJson('/api/payments/collect', [
            'student_id' => $this->student->id,
            'student_fee_id' => $studentFee->id,
            'amount' => 800.00,
            'payment_method' => 'upi',
            'payment_reference' => 'UPI987654321',
            'note' => 'Partial installment',
        ]);

        $collectResponse->assertStatus(201);
        $collectResponse->assertJson(['success' => true]);

        $paymentId = $collectResponse->json('data.payment.id');
        $receiptNo = $collectResponse->json('data.receipt_no');
        $paymentNo = $collectResponse->json('data.payment_no');

        $this->assertNotNull($receiptNo);
        $this->assertNotNull($paymentNo);
        $this->assertStringStartsWith('REC-', $receiptNo);
        $this->assertStringStartsWith('PAY-', $paymentNo);

        // Check balances
        $studentFee->refresh();
        $this->assertEquals(800.00, (float)$studentFee->paid_amount);
        $this->assertEquals(1200.00, (float)$studentFee->balance_amount);
        $this->assertEquals('partial', $studentFee->status);

        // Check Receipt API
        $receiptResponse = $this->getJson("/api/payments/{$paymentId}/receipt");
        $receiptResponse->assertStatus(200);
        $receiptResponse->assertJson([
            'success' => true,
            'data' => [
                'receipt_no' => $receiptNo,
                'financials' => [
                    'total_paid' => 800.00,
                    'outstanding_balance' => 1200.00,
                ],
            ]
        ]);

        // 2. Collect remaining payment: 1200 to fully clear
        $secondCollect = $this->postJson('/api/payments/collect', [
            'student_id' => $this->student->id,
            'student_fee_id' => $studentFee->id,
            'amount' => 1200.00,
            'payment_method' => 'cash',
        ]);
        $secondCollect->assertStatus(201);

        $studentFee->refresh();
        $this->assertEquals(2000.00, (float)$studentFee->paid_amount);
        $this->assertEquals(0.00, (float)$studentFee->balance_amount);
        $this->assertEquals('paid', $studentFee->status);

        // Check Student Profile ledger
        $profileResponse = $this->getJson("/api/student-fees/student/{$this->student->id}");
        $profileResponse->assertStatus(200);
        $profileResponse->assertJson([
            'success' => true,
            'data' => [
                'summary' => [
                    'total_assigned' => 2000.00,
                    'total_paid' => 2000.00,
                    'total_balance_due' => 0.00,
                    'clearance_status' => 'Clear',
                ]
            ]
        ]);
    }

    public function test_payment_reversal_workflow_and_deletion_prevention()
    {
        $group = FeeGroup::create(['name' => 'General']);
        $feeType = FeeType::create(['fee_group_id' => $group->id, 'name' => 'Quarterly', 'amount' => 1000]);

        $structure = FeeStructure::create([
            'name' => 'Structure Reversal Test',
            'class_id' => $this->class->id,
            'total_amount' => 1000.00,
        ]);
        $structure->items()->create(['fee_type_id' => $feeType->id, 'amount' => 1000.00]);

        $this->postJson('/api/student-fees/assign', [
            'fee_structure_id' => $structure->id,
            'student_ids' => [$this->student->id],
        ]);

        $studentFee = StudentFee::where('student_id', $this->student->id)->first();

        $collect = $this->postJson('/api/payments/collect', [
            'student_id' => $this->student->id,
            'student_fee_id' => $studentFee->id,
            'amount' => 1000.00,
            'payment_method' => 'cheque',
            'payment_reference' => 'CHQ123456',
        ]);
        $paymentId = $collect->json('data.payment.id');

        $studentFee->refresh();
        $this->assertEquals(0.00, (float)$studentFee->balance_amount);
        $this->assertEquals('paid', $studentFee->status);

        // Test payment permanent delete is rejected
        $deleteResponse = $this->deleteJson("/api/payments/{$paymentId}");
        $deleteResponse->assertStatus(403);
        $this->assertDatabaseHas('payments', ['id' => $paymentId]);

        // Reverse payment
        $reverseResponse = $this->postJson("/api/payments/{$paymentId}/reverse", [
            'reversal_reason' => 'Cheque returned unpaid / bounced',
        ]);

        $reverseResponse->assertStatus(200);
        $reverseResponse->assertJson(['success' => true]);

        // Verify payment marked as reversed
        $this->assertDatabaseHas('payments', [
            'id' => $paymentId,
            'status' => 'reversed',
            'reversal_reason' => 'Cheque returned unpaid / bounced',
        ]);

        // Verify student fee balance rolled back
        $studentFee->refresh();
        $this->assertEquals(0.00, (float)$studentFee->paid_amount);
        $this->assertEquals(1000.00, (float)$studentFee->balance_amount);
        $this->assertEquals('pending', $studentFee->status);

        // Test cannot reverse again
        $secondReverse = $this->postJson("/api/payments/{$paymentId}/reverse", [
            'reversal_reason' => 'Duplicate reversal attempt',
        ]);
        $secondReverse->assertStatus(422);
    }
}
