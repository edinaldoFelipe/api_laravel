<?php

namespace Tests\Feature\Controllers;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Http\Response;
use App\Models\V1\Task;
use Faker\Generator;
use Faker\Factory;


class TasksControllerTest extends TestCase
{
    private Generator $faker;

    /*
    |-------------------------------------------
    | SUCESSFULLY
    |-------------------------------------------
    */

    /**
     * Test index route api.
     * Select and valid response format data
     *
     * @return void
     */
    public function testIndexReturnsDataInValidFormat()
    {
        $this->json('get', 'api/v1/tasks')
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure(
                [
                    '*' => [
                        'id',
                        'name',
                        'description',
                        'status',
                        'file_url',
                        'tags',
                        'created_at',
                        'updated_at',
                    ]
                ]
            );
    }

    /**
     * Test post route api
     * Create and check register
     *
     * @return void
     */
    public function testTaskIsCreatedSuccessfully()
    {
        $this->faker = Factory::create();

        $payload = [
            'name' => $this->faker->realText(30, 1),
            'description' => $this->faker->text,
            'status' => $this->faker->randomElement(['BACKLOG', 'IN_PROGRESS', 'WAITING_CUSTOMER_APPROVAL', 'APPROVED']),
            'file_url' => $this->faker->url,
        ];

        $this->json('post', 'api/v1/tasks', $payload)
            ->assertStatus(Response::HTTP_CREATED)
            ->assertJsonStructure(
                [
                    'id',
                    'name',
                    'description',
                    'status',
                    'file_url',
                    'created_at',
                    'updated_at',
                ]
            );
        $this->assertDatabaseHas('tasks', $payload);
    }

    /**
     * Test put route api
     * Create task, update and compare response
     *
     * @return void
     */
    public function testTaskIsUpdateSuccessfully()
    {
        $this->faker = Factory::create();

        // create new Task
        $task = Task::create(
            [
                'name' => $this->faker->realText(30, 1),
                'description' => $this->faker->text,
                'status' => 'BACKLOG',
                'file_url' => $this->faker->url,
            ]
        );

        // data to update
        $payload = [
            'name' => 'New Name',
            'description' => null,
            'status' => 'BACKLOG',
            'file_url' => 'https://www.mandarin.com.br/',
        ];

        $this->json('put', "api/v1/tasks/$task->id", $payload)
            ->assertStatus(Response::HTTP_OK)
            ->assertExactJson(
                [
                    'id' => $task->id,
                    'name' => $payload['name'],
                    'description' => $payload['description'],
                    'status' => $payload['status'],
                    'file_url' =>  $payload['file_url'],
                    'created_at' => $task->created_at,
                    'updated_at' => $task->created_at,
                ]
            );
    }

    /**
     * Test get file_url route api
     *
     * @return void
     */
    public function testTaskFileUrlIsShownCorrectly()
    {
        $this->faker = Factory::create();

        // create new task
        $task = Task::create(
            [
                'name' => $this->faker->realText(30, 1),
                'description' => $this->faker->text,
                'status' => 'approved',
                'file_url' => $this->faker->url,
            ]
        );

        // get only file_url
        $this->get("api/v1/tasks/$task->id/file_url")
            ->assertStatus(Response::HTTP_OK)
            ->assertSee($task['file_url']);
    }

    /**
     * Test post route api
     * Create and check register
     *
     * @return void
     */
    public function testTaskTagIsCreatedSuccessfully()
    {
        $this->faker = Factory::create();

        // get random task
        $task = Task::inRandomOrder()->first();

        // data to new tag
        $payload = [
            'tag_name' => $this->faker->randomElement(['warning', 'todo', 'bug', 'urgent']),
            'task_id' => $task->id,
        ];

        $this->post("api/v1/tasks/$task->id/tag", $payload)
            ->assertStatus(Response::HTTP_NO_CONTENT);

        $this->assertDatabaseHas('tags', $payload);
    }

    /**
     * Test patch route api
     * Create, updated and check status
     *
     * @return void
     */
    public function testTagStautsIsUpdatedSuccessfully()
    {
        $this->faker = Factory::create();

        // create a new task
        $task = Task::create(
            [
                'name' => $this->faker->realText(30, 1),
                'description' => $this->faker->text,
                'status' => 'BACKLOG',
                'file_url' => $this->faker->url,
            ]
        );

        // data to update
        $payload = [
            'status' => 'IN_PROGRESS',
        ];

        $this->patch("api/v1/tasks/$task->id/status", $payload)
            ->assertStatus(Response::HTTP_NO_CONTENT);

        $this->assertDatabaseHas('tasks', $payload);
    }

    /*
    |-------------------------------------------
    | FAILS
    |-------------------------------------------
    */
}
