<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Expense;
use App\Models\Todo;
use App\Models\Event;
use App\Models\ClassRoom;
use App\Models\Student;
use App\Models\Homework;

class SoftDeletesTest extends TestCase
{
    public function test_models_perform_soft_delete()
    {
        // 1. Test Expense soft delete
        $expense = Expense::create([
            'title'       => 'Temporary Test Expense',
            'amount'      => 150.00,
            'date'        => now()->format('Y-m-d'),
            'description' => 'Soft delete test',
        ]);

        $this->assertNull($expense->deleted_at);
        $expenseId = $expense->id;

        // Perform soft delete
        $expense->delete();

        // Model should be trashed
        $this->assertTrue($expense->trashed());
        $this->assertNotNull($expense->fresh()->deleted_at);

        // Should not be found via normal query
        $this->assertNull(Expense::find($expenseId));

        // Should be found with withTrashed()
        $this->assertNotNull(Expense::withTrashed()->find($expenseId));

        // Restore
        $expense->restore();
        $this->assertFalse($expense->fresh()->trashed());
        $this->assertNotNull(Expense::find($expenseId));

        // Force delete cleanup
        $expense->forceDelete();
        $this->assertNull(Expense::withTrashed()->find($expenseId));
    }

    public function test_todo_soft_delete()
    {
        $user = User::first() ?? User::factory()->create();

        $todo = Todo::create([
            'user_id'      => $user->id,
            'title'        => 'Test soft delete todo',
            'is_completed' => false,
            'date'         => now()->format('Y-m-d'),
        ]);

        $todoId = $todo->id;
        $todo->delete();

        $this->assertTrue($todo->trashed());
        $this->assertNull(Todo::find($todoId));
        $this->assertNotNull(Todo::withTrashed()->find($todoId));

        $todo->forceDelete();
        $this->assertNull(Todo::withTrashed()->find($todoId));
    }

    public function test_classroom_soft_delete()
    {
        $room = ClassRoom::create([
            'room_no'     => 'Room 101',
            'capacity'    => 35,
            'description' => 'Test room for soft delete',
        ]);

        $roomId = $room->id;
        $room->delete();

        $this->assertTrue($room->trashed());
        $this->assertNull(ClassRoom::find($roomId));
        $this->assertNotNull(ClassRoom::withTrashed()->find($roomId));

        $room->forceDelete();
        $this->assertNull(ClassRoom::withTrashed()->find($roomId));
    }
}
