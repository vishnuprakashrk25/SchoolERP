<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Student;
use App\Models\Staff;
use App\Models\Teacher;
use App\Models\StudentParent;
use App\Models\StudentFee;
use App\Models\StudentAttendance;
use App\Models\StaffAttendance;
use App\Models\Classes;
use App\Models\Section;
use App\Models\AcademicYear;
use Carbon\Carbon;

class DashboardNotificationCardTest extends TestCase
{
    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::first() ?? User::factory()->create();
    }

    public function test_dashboard_returns_card_stats_with_contextual_messages()
    {
        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson('/api/dashboard');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);

        $cardStats = $response->json('data.card_stats');

        $this->assertNotNull($cardStats);
        $this->assertArrayHasKey('students', $cardStats);
        $this->assertArrayHasKey('teachers', $cardStats);
        $this->assertArrayHasKey('parents', $cardStats);
        $this->assertArrayHasKey('staffs', $cardStats);

        foreach (['students', 'teachers', 'parents', 'staffs'] as $key) {
            $card = $cardStats[$key];
            $this->assertArrayHasKey('total_count', $card);
            $this->assertArrayHasKey('new_this_month', $card);
            $this->assertArrayHasKey('pending_fee_payments', $card);
            $this->assertArrayHasKey('notification_message', $card);
            $this->assertArrayHasKey('messages', $card);
            $this->assertIsArray($card['messages']);
            $this->assertNotEmpty($card['messages'], "Card {$key} should have at least one notification message");

            // Verify structure of each message in the array
            foreach ($card['messages'] as $msg) {
                $this->assertArrayHasKey('text', $msg);
                $this->assertArrayHasKey('category', $msg);
                $this->assertArrayHasKey('type', $msg);
                $this->assertArrayHasKey('count', $msg);
                $this->assertArrayHasKey('link', $msg);

                $this->assertContains($msg['type'], ['warning', 'danger', 'info', 'success']);
                $this->assertIsString($msg['text']);
            }
        }
    }

    public function test_attendance_and_fee_messages_reflect_real_data()
    {
        $today = Carbon::today()->toDateString();

        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson('/api/dashboard');

        $response->assertStatus(200);
        $studentMessages = $response->json('data.card_stats.students.messages');
        
        $categories = array_column($studentMessages, 'category');
        $this->assertContains('attendance', $categories, "Student card should include attendance message");
        $this->assertContains('payment', $categories, "Student card should include payment message");
    }
}
