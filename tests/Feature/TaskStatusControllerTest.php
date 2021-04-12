<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\TaskStatus;
use TaskStatusSeeder;
use UserSeeder;

class TaskStatusControllerTest extends TestCase
{
    private User $user;
    private TaskStatus $taskStatus;
    private string $newTaskStatusName;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(TaskStatusSeeder::class);
        $this->seed(UserSeeder::class);
        $this->user = User::find(1);
        $this->taskStatus = TaskStatus::find(1);
        $this->newTaskStatusName = 'test';
    }

    public function testIndex()
    {
        $response = $this->get(route('task_statuses.index'));
        $response->assertOk();
    }


    public function testCreate()
    {
        $response = $this->actingAs($this->user)
            ->get(route('task_statuses.create'));
        $response->assertOk();
    }

    public function testStore()
    {
        $this->withoutMiddleware();
        $response = $this->actingAs($this->user)
        ->post(route('task_statuses.store'), ['name' => $this->newTaskStatusName]);
        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('task_statuses', $this->taskStatus->only(['name']));
        $response->assertRedirect();
    }

    public function testEdit()
    {
        $response = $this->actingAs($this->user)
            ->get(route('task_statuses.edit', $this->taskStatus));
        $response->assertOk();
    }

    public function testUpdate()
    {
        $this->withoutMiddleware();
        $taskStatus = TaskStatus::find(1);
        $oldName = "Новый";
        $testName = "test name";
        $response = $this->actingAs($this->user)
            ->patch(route('task_statuses.update', $taskStatus), [
                'name' => $testName,
            ]);
        $response->assertSessionHasNoErrors();
        $response->assertRedirect();
        $this->assertDatabaseMissing('task_statuses', ['name' => $oldName]);
        $this->assertDatabaseHas('task_statuses', [
            'name' => $testName,
        ]);
    }

    public function testDestroy()
    {
        $this->withoutMiddleware('App\Http\Middleware\VerifyCsrfToken');
        $taskStatus = TaskStatus::find(2);
        $response = $this->actingAs($this->user)
            ->delete(route('task_statuses.destroy', $taskStatus));
        $response->assertSessionHasNoErrors();
        $response->assertRedirect();
        $this->assertDatabaseMissing('task_statuses', $taskStatus->only(['id']));
    }
}
