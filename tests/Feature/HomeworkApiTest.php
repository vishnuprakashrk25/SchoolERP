<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Classes;
use App\Models\Section;
use App\Models\Subject;
use App\Models\Student;
use App\Models\Homework;
use App\Models\HomeworkSubmission;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class HomeworkApiTest extends TestCase
{
    protected $user;
    protected $class;
    protected $section;
    protected $subject;
    protected $student;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::first() ?? User::factory()->create();
        $this->class = Classes::first();
        $this->section = Section::first();
        $this->subject = Subject::first();
        $this->student = Student::first();
    }

    public function test_can_list_homeworks()
    {
        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson('/api/homeworks');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data',
                'meta' => ['current_page', 'last_page', 'per_page', 'total']
            ]);
    }

    public function test_can_create_homework_with_file_and_auto_submissions()
    {
        Storage::fake('public');
        $file = UploadedFile::fake()->create('sample_assignment.pdf', 500, 'application/pdf');

        $payload = [
            'class_id'        => $this->class->id,
            'section_id'      => $this->section->id,
            'subject_id'      => $this->subject->id,
            'homework_date'   => now()->format('Y-m-d'),
            'submission_date' => now()->addDays(7)->format('Y-m-d'),
            'marks'           => 50,
            'description'     => 'Solve all chapter 3 questions',
            'file'            => $file,
        ];

        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/homeworks', $payload);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'message' => 'Homework created successfully'
            ]);

        $hwId = $response->json('data.id');
        $this->assertNotNull($hwId);

        // Verify homework in database
        $this->assertDatabaseHas('homeworks', [
            'id' => $hwId,
            'marks' => 50,
        ]);

        return $hwId;
    }

    public function test_full_homework_lifecycle()
    {
        // 1. Create homework
        $createRes = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/homeworks', [
                'class_id'        => $this->class->id,
                'section_id'      => $this->section->id,
                'subject_id'      => $this->subject->id,
                'homework_date'   => now()->format('Y-m-d'),
                'submission_date' => now()->addDays(5)->format('Y-m-d'),
                'marks'           => 100,
                'description'     => 'Full cycle test assignment',
            ]);

        $createRes->assertStatus(201);
        $hwId = $createRes->json('data.id');

        // Ensure this student has a submission record
        $submission = HomeworkSubmission::firstOrCreate([
            'homework_id' => $hwId,
            'student_id'  => $this->student->id,
        ], [
            'status' => 'pending'
        ]);

        // 2. Show homework
        $showRes = $this->actingAs($this->user, 'sanctum')
            ->getJson("/api/homeworks/{$hwId}");

        $showRes->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'id' => $hwId,
                    'marks' => 100,
                ]
            ]);

        // 3. Student submits homework
        $file = UploadedFile::fake()->create('answer_sheet.pdf', 300, 'application/pdf');
        $submitRes = $this->actingAs($this->user, 'sanctum')
            ->postJson("/api/homeworks/{$hwId}/submit", [
                'student_id'      => $this->student->id,
                'submission_file' => $file,
                'student_notes'   => 'Here is my completed assignment',
            ]);

        $submitRes->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Homework submitted successfully'
            ]);

        // 4. Teacher evaluates submission
        $evalRes = $this->actingAs($this->user, 'sanctum')
            ->postJson("/api/homeworks/{$hwId}/evaluate", [
                'student_id'  => $this->student->id,
                'marks'       => 92,
                'description' => 'Excellent work on problem sets',
                'status'      => 'completed'
            ]);

        $evalRes->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Homework evaluated successfully'
            ]);

        $submissionId = $submission->fresh()->id;

        // 5. Evaluation Summary Modal
        $summaryRes = $this->actingAs($this->user, 'sanctum')
            ->getJson("/api/homework-report/evaluation/{$submissionId}");

        $summaryRes->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'header' => [
                        'marks_badge' => '92/100',
                    ],
                    'summary' => [
                        'marks' => 100,
                        'obtained_marks' => 92,
                    ]
                ]
            ]);

        // 6. Homework Report
        $reportRes = $this->actingAs($this->user, 'sanctum')
            ->getJson("/api/homework-report?class_id={$this->class->id}");

        $reportRes->assertStatus(200)
            ->assertJson(['success' => true]);

        // 7. Update homework
        $updateRes = $this->actingAs($this->user, 'sanctum')
            ->postJson("/api/homeworks/{$hwId}", [
                'description' => 'Updated assignment description',
                'marks'       => 100,
            ]);

        $updateRes->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Homework updated successfully'
            ]);

        // 8. Delete homework
        $deleteRes = $this->actingAs($this->user, 'sanctum')
            ->deleteJson("/api/homeworks/{$hwId}");

        $deleteRes->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Homework deleted successfully'
            ]);
    }
}
