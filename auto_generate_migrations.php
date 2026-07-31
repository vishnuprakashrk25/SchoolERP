<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Str;

$models = glob(__DIR__ . '/app/Models/*.php');

$missingModels = [
    'Exam', 'ExamResult', 'ExamSchedule', 'FeeGroup', 'FeeStructure', 
    'FeeType', 'Payment', 'SchoolClass', 'Staff', 'StaffAttendance', 
    'StaffDepartment', 'StudentAttendance', 'StudentFee', 
    'StudentMultiClass', 'Timetable'
];

$migrationsDir = __DIR__ . '/database/migrations/';

// Use a distinct timestamp to group these generated migrations
$timestampBase = date('Y_m_d_His');
$counter = 0;

foreach ($missingModels as $modelName) {
    $class = 'App\Models\\' . $modelName;
    if (!class_exists($class)) {
        echo "Class not found: $class\n";
        continue;
    }

    try {
        $model = new $class;
        $table = $model->getTable();
        
        if (Illuminate\Support\Facades\Schema::hasTable($table)) {
            echo "Table $table already exists. Skipping.\n";
            continue;
        }

        $fillable = $model->getFillable();
        
        $columns = [];
        $columns['id'] = "    \$table->id();\n";

        foreach ($fillable as $field) {
            if ($field === 'id') continue;

            if (Str::endsWith($field, '_id')) {
                // Determine foreign table
                $refTable = Str::plural(str_replace('_id', '', $field));
                
                // Special mappings based on likely database foreign key constraints
                if ($refTable === 'academic_years') {
                    $columns[$field] = "    \$table->foreignId('$field')->nullable()->constrained('academic_years')->nullOnDelete();\n";
                } elseif ($refTable === 'users' || $field === 'user_id' || $field === 'student_id' || $field === 'parent_id') {
                    // It's safer to just do a normal bigInteger for very generic relationships to avoid hard crashes if the table implies something weird
                    // Let's use constrained where obvious, or just integer for safety.
                    if ($field === 'student_id') {
                        $columns[$field] = "    \$table->foreignId('$field')->nullable()->constrained('students')->cascadeOnDelete();\n";
                    } else if ($field === 'class_id') {
                        $columns[$field] = "    \$table->foreignId('$field')->nullable()->constrained('classes')->nullOnDelete();\n";
                    } else if ($field === 'section_id') {
                        $columns[$field] = "    \$table->foreignId('$field')->nullable()->constrained('sections')->nullOnDelete();\n";
                    } else if ($field === 'branch_id') {
                        $columns[$field] = "    \$table->foreignId('$field')->nullable()->constrained('branches')->nullOnDelete();\n";
                    } else if ($field === 'department_id' && $table === 'staff') {
                        $columns[$field] = "    \$table->foreignId('$field')->nullable()->constrained('staff_departments')->nullOnDelete();\n";
                    } else if ($field === 'staff_id') {
                        $columns[$field] = "    \$table->foreignId('$field')->nullable()->constrained('staff')->nullOnDelete();\n";
                    } else if ($field === 'subject_id') {
                        $columns[$field] = "    \$table->foreignId('$field')->nullable()->constrained('subjects')->nullOnDelete();\n";
                    } else if ($field === 'exam_id') {
                        $columns[$field] = "    \$table->foreignId('$field')->nullable()->constrained('exams')->cascadeOnDelete();\n";
                    } else {
                        $columns[$field] = "    \$table->unsignedBigInteger('$field')->nullable();\n";
                    }
                } else {
                    $columns[$field] = "    \$table->unsignedBigInteger('$field')->nullable();\n";
                }
            } elseif (in_array($field, ['date', 'exam_date', 'payment_date', 'due_date', 'start_date', 'end_date'])) {
                $columns[$field] = "    \$table->date('$field')->nullable();\n";
            } elseif (in_array($field, ['amount', 'fee_amount', 'paid_amount', 'balance', 'total_marks', 'passing_marks', 'marks_obtained'])) {
                $columns[$field] = "    \$table->decimal('$field', 10, 2)->default(0);\n";
            } elseif (in_array($field, ['description', 'remarks', 'address', 'details', 'note'])) {
                $columns[$field] = "    \$table->text('$field')->nullable();\n";
            } elseif ($field === 'is_active' || $field === 'status' || strpos($field, 'is_') === 0) {
                if ($field === 'status') {
                    $columns[$field] = "    \$table->string('$field')->default('active');\n";
                } else {
                    $columns[$field] = "    \$table->boolean('$field')->default(true);\n";
                }
            } else {
                $columns[$field] = "    \$table->string('$field')->nullable();\n";
            }
        }

        $columns['timestamps'] = "    \$table->timestamps();\n";
        $columns['softDeletes'] = "    \$table->softDeletes();\n";

        $schemaBlock = implode("", $columns);

        $migrationTemplate = <<<PHP
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('$table', function (Blueprint \$table) {
$schemaBlock
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('$table');
    }
};
PHP;
        
        // Generate timestamp
        $counter++;
        $ts = date('Y_m_d_His', strtotime("+$counter seconds", strtotime(date('Y-m-d H:i:s'))));
        $filename = "{$ts}_create_{$table}_table.php";
        
        file_put_contents($migrationsDir . $filename, $migrationTemplate);
        
        echo "Generated migration for $table ({$filename})\n";

    } catch (\Throwable $e) {
        echo "Error on $modelName: " . $e->getMessage() . "\n";
    }
}

echo "All missing migrations generated.\n";
