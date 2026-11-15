<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Classes;
use App\Models\Section;
use App\Models\Student;
use App\Models\AcademicYear;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class StudentPhotoUpdateTest extends TestCase
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
        $this->academicYear = AcademicYear::first();
        $this->class = Classes::first();

        $this->student = Student::create([
            'admission_no'     => 'PHOTO_TEST_' . rand(10000, 99999),
            'first_name'       => 'PhotoTest',
            'last_name'        => 'User',
            'full_name'        => 'PhotoTest User',
            'class_id'         => $this->class?->id,
            'academic_year_id' => $this->academicYear?->id,
            'status'           => 1,
        ]);
    }

    public function test_can_update_student_photo_via_post_multipart()
    {
        Storage::fake('public');
        $file = UploadedFile::fake()->image('avatar.jpg', 200, 200);

        $response = $this->actingAs($this->user, 'sanctum')
            ->post("/api/students/{$this->student->id}", [
                'photo' => $file,
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Student updated',
            ]);

        $this->student->refresh();
        $this->assertNotNull($this->student->photo);
        $this->assertStringContainsString('uploads/student_photos/', $this->student->photo);
    }

    public function test_can_update_student_photo_via_base64_string()
    {
        // 1x1 transparent png in base64
        $base64 = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNkYAAAAAYAAjCB0C8AAAAASUVORK5CYII=';

        $response = $this->actingAs($this->user, 'sanctum')
            ->putJson("/api/students/{$this->student->id}", [
                'photo' => $base64,
            ]);

        $response->assertStatus(200);

        $this->student->refresh();
        $this->assertNotNull($this->student->photo);
        $this->assertStringContainsString('uploads/student_photos/', $this->student->photo);
    }

    public function test_all_other_fields_update_properly()
    {
        $response = $this->actingAs($this->user, 'sanctum')
            ->putJson("/api/students/{$this->student->id}", [
                'full_name'        => 'Updated All Fields Student',
                'first_name'       => 'Updated',
                'last_name'        => 'All Fields',
                'roll_no'          => '55',
                'dob'              => '2012-05-15',
                'gender'           => 'female',
                'blood_group'      => 'A+',
                'religion'         => 'Hinduism',
                'caste'            => 'General',
                'phone'            => '9876543210',
                'emergency_phone'  => '9123456780',
                'current_address'  => '123 New Street, City',
                'permanent_address'=> '456 Old Street, City',
                'bank_account_no'  => '987654321000',
                'bank_name'        => 'State Bank of India',
                'ifsc_code'        => 'SBIN0001234',
                'national_id_no'   => 'NAT12345678',
                'apaar_id'         => 'APAAR999888',
                'aadharshila_no'   => 'AADH777666',
                'pen_no'           => 'PEN555444',
                'note'             => 'Test note for student update',
                'father_name'      => 'Father Name Test',
                'father_phone'     => '9888777666',
                'mother_name'      => 'Mother Name Test',
                'mother_phone'     => '9777666555',
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Student updated',
            ]);

        $this->student->refresh();
        $this->assertEquals('Updated All Fields Student', $this->student->full_name);
        $this->assertEquals('Updated', $this->student->first_name);
        $this->assertEquals('All Fields', $this->student->last_name);
        $this->assertEquals('55', $this->student->roll_no);
        $this->assertEquals('female', $this->student->gender);
        $this->assertEquals('A+', $this->student->blood_group);
        $this->assertEquals('Hinduism', $this->student->religion);
        $this->assertEquals('General', $this->student->caste);
        $this->assertEquals('9876543210', $this->student->phone);
        $this->assertEquals('9123456780', $this->student->emergency_phone);
        $this->assertEquals('APAAR999888', $this->student->apaar_id);
        $this->assertEquals('AADH777666', $this->student->aadharshila_no);
        $this->assertEquals('PEN555444', $this->student->pen_no);

        // Check parent fields updated
        $parent = $this->student->parents;
        $this->assertNotNull($parent);
        $this->assertEquals('Father Name Test', $parent->father_name);
        $this->assertEquals('Mother Name Test', $parent->mother_name);
    }
}
