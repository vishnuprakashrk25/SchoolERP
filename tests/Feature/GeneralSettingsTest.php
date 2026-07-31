<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\GeneralSetting;
use App\Models\AcademicYear;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class GeneralSettingsTest extends TestCase
{
    use DatabaseTransactions;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::create([
            'name' => 'Settings Admin',
            'email' => 'settings_admin_' . uniqid() . '@example.com',
            'password' => bcrypt('password'),
            'branch_id' => 1,
            'is_active' => true
        ]);
    }

    public function test_get_general_settings_initializes_and_returns_defaults()
    {
        // Assert no settings row exists initially (clean database transaction context)
        GeneralSetting::truncate();

        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson('/api/general-settings');

        $response->assertStatus(200);
        $response->assertJsonPath('success', true);
        $response->assertJsonStructure([
            'success',
            'data' => [
                'id',
                'school_name',
                'site_title'
            ]
        ]);

        $this->assertEquals('My School', $response->json('data.school_name'));
        $this->assertEquals('School ERP', $response->json('data.site_title'));
    }

    public function test_update_general_settings_saves_correctly()
    {
        $year = AcademicYear::create([
            'name' => '2026-2027',
            'start_date' => '2026-04-01',
            'end_date' => '2027-03-31',
            'is_current' => true
        ]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->putJson('/api/general-settings', [
                'school_name' => 'Raza Ul Uloom Higher Secondary School',
                'site_title' => 'RUIHSS',
                'email' => 'admin@ruihss.in',
                'phone' => '+917298010134',
                'academic_year_id' => $year->id,
                'currency' => 'INR',
                'currency_symbol' => '₹',
                'carry_forward_fees_due_days' => 60
            ]);

        $response->assertStatus(200);
        $response->assertJsonPath('success', true);
        $this->assertEquals('Raza Ul Uloom Higher Secondary School', $response->json('data.school_name'));
        $this->assertEquals('INR', $response->json('data.currency'));
        $this->assertEquals(60, $response->json('data.carry_forward_fees_due_days'));
    }

    public function test_update_general_settings_validation_errors()
    {
        $response = $this->actingAs($this->user, 'sanctum')
            ->putJson('/api/general-settings', [
                'school_name' => '', // Required
                'site_title' => '',   // Required
                'email' => 'not-an-email'
            ]);

        $response->assertStatus(422);
        $response->assertJsonPath('success', false);
        $response->assertJsonStructure(['success', 'errors' => ['school_name', 'site_title', 'email']]);
    }

    public function test_upload_logo_success()
    {
        Storage::fake('public');

        $file = UploadedFile::fake()->image('school_logo.png');

        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/general-settings/logo', [
                'logo' => $file
            ]);

        $response->assertStatus(200);
        $response->assertJsonPath('success', true);
        $response->assertJsonStructure(['success', 'logo_url', 'data']);

        $settings = GeneralSetting::first();
        $this->assertNotNull($settings->logo);
    }

    public function test_upload_favicon_success()
    {
        Storage::fake('public');

        $file = UploadedFile::fake()->image('favicon.png');

        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/general-settings/favicon', [
                'favicon' => $file
            ]);

        $response->assertStatus(200);
        $response->assertJsonPath('success', true);
        $response->assertJsonStructure(['success', 'favicon_url', 'data']);

        $settings = GeneralSetting::first();
        $this->assertNotNull($settings->favicon);
    }
}
