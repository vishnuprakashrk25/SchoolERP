<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\PermissionRegistrar;

class CreateSuperAdminCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:super-admin 
                            {--name=SaaS Super Admin : The name of the super admin}
                            {--email=superadmin@erp.com : The login email for the super admin}
                            {--password=password : The login password for the super admin}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create or update a SaaS Super Admin user with full platform privileges';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $name     = $this->option('name');
        $email    = $this->option('email');
        $password = $this->option('password');

        $this->info("Creating/updating SaaS Super Admin [{$email}]...");

        // 1. Reset permission cache
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // 2. Ensure super-admin role exists for web and api/sanctum guards
        $roleWeb = Role::firstOrCreate(['name' => 'super-admin', 'guard_name' => 'web']);
        $roleSanctum = Role::firstOrCreate(['name' => 'super-admin', 'guard_name' => 'sanctum']);

        // Give all permissions to super-admin
        $allPermissions = Permission::all();
        if ($allPermissions->isNotEmpty()) {
            $roleWeb->syncPermissions($allPermissions);
        }

        // 3. Create or update the user with branch_id = null (global SaaS scope)
        $user = User::updateOrCreate(
            ['email' => $email],
            [
                'name'      => $name,
                'full_name' => $name,
                'password'  => Hash::make($password),
                'branch_id' => null, // Global scope (not tied to a single school/branch)
                'is_active' => true,
            ]
        );

        // 4. Assign super-admin role
        $user->syncRoles(['super-admin']);

        $this->newLine();
        $this->info("=================================================");
        $this->info("  SaaS Super Admin Created Successfully!         ");
        $this->info("=================================================");
        $this->line("  ID:       {$user->id}");
        $this->line("  Name:     {$user->name}");
        $this->line("  Email:    {$user->email}");
        $this->line("  Password: {$password}");
        $this->line("  Role:     super-admin");
        $this->line("  Branch:   Global (NULL - All Schools/Tenants)");
        $this->info("=================================================");
        $this->newLine();

        return Command::SUCCESS;
    }
}
