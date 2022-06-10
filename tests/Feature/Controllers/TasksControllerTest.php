<?php

namespace Tests\Feature\Controllers;

use Tests\TestCase;
use Illuminate\Http\Response;
use App\Models\V1\Task;
use App\Models\V1\Tags;
use Faker\Generator;
use Faker\Factory;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TasksControllerTest extends TestCase
{
    use RefreshDatabase;
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
        $this->faker = Factory::create();

        // create task
        $task = Task::create(
            [
                'name' => $this->faker->realText(30, 1),
                'description' => $this->faker->text,
                'status' => 'BACKLOG',
                'file_url' => $this->faker->url,
            ]
        );

        //create tag
        Tags::create(
            [
                'tag_name' => 'WARNING',
                'task_id' => $task->id,
            ]
        );

        $this->json('get', 'api/v1/tasks')
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure(
                [
                    '*' => [
                        'id',
                        'name',
                        'description',
                        'status',
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
            'status' => 'IN_PROGRESS',
            'file_url' => 'https://www.mandarin.com.br/',
        ];

        $this->json('put', "api/v1/tasks/$task->id", $payload)
            ->assertStatus(Response::HTTP_NO_CONTENT);
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
                'status' => 'APPROVED',
                'file_url' => $this->faker->url,
            ]
        );

        // get only file_url
        $this->get("api/v1/tasks/$task->id/file_url")
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure(
                [
                    'file_url',
                ]
            );
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

        // create new task
        $task = Task::create(
            [
                'name' => $this->faker->realText(30, 1),
                'description' => $this->faker->text,
                'status' => 'BACKLOG',
                'file_url' => $this->faker->url,
            ]
        );

        // data to new tag
        $payload = [
            'tag_name' => $this->faker->randomElement(['WARNING', 'TODO', 'BUG', 'URGENT']),
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
    public function testTagStatusIsUpdatedSuccessfully()
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

    /**
     * Test error post route api
     * Dont send status
     *
     * @return void
     */
    public function testTaskIsCreatedErrorNullField()
    {
        $this->faker = Factory::create();

        $payload = [
            'name' => $this->faker->realText(30, 1),
            'description' => $this->faker->text,
            'status' => null,
            'file_url' => $this->faker->url,
        ];

        $this->json('post', 'api/v1/tasks', $payload)
            ->assertStatus(Response::HTTP_BAD_REQUEST)
            ->assertJsonStructure(
                [
                    'error',
                    'message',
                ]
            );
    }

    /**
     * Test error put route api
     * Send Name null
     *
     * @return void
     */
    public function testTaskIsUpdateErrorNameNull()
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
            'name' => null,
            'status' => 'BACKLOG',
            'file_url' => 'https://www.mandarin.com.br/',
        ];

        $this->json('put', "api/v1/tasks/$task->id", $payload)
            ->assertStatus(Response::HTTP_BAD_REQUEST)
            ->assertJsonStructure(
                [
                    'error',
                    'message',
                ]
            );
    }

    /**
     * Test error patch route api
     * Send status wrong order
     *
     * @return void
     */
    public function testTagStatusIsUpdatedErrorJumpStep()
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
            'status' => 'APPROVED',
        ];

        $this->patch("api/v1/tasks/$task->id/status", $payload)
            ->assertStatus(Response::HTTP_BAD_REQUEST)
            ->assertJsonStructure(
                [
                    'error',
                    'message',
                ]
            );
    }

    /**
     * Test error post route api
     * Send duplicated tag
     *
     * @return void
     */
    public function testTaskTagIsCreatedErrorDuplicateTag()
    {
        $this->faker = Factory::create();

        // create new task
        $task = Task::create(
            [
                'name' => $this->faker->realText(30, 1),
                'description' => $this->faker->text,
                'status' => 'BACKLOG',
                'file_url' => $this->faker->url,
            ]
        );

        // create new tag
        $tag = Tags::create(
            [
                'tag_name' => 'WARNING',
                'task_id' => $task->id,
            ]
        );

        // data to new tag
        $payload = [
            'tag_name' => $tag->tag_name,
            'task_id' => $task->id,
        ];

        $this->post("api/v1/tasks/$task->id/tag", $payload)
            ->assertStatus(Response::HTTP_BAD_REQUEST)
            ->assertJsonStructure(
                [
                    'error',
                    'message',
                ]
            );
    }

    /**
     * Test error file_url route api
     * Link don't allowed
     *
     * @return void
     */
    public function testTaskFileUrlIsShowForbidden()
    {
        $this->faker = Factory::create();

        // create new task
        $task = Task::create(
            [
                'name' => $this->faker->realText(30, 1),
                'description' => $this->faker->text,
                'status' => 'BACKLOG',
                'file_url' => $this->faker->url,
            ]
        );

        // get only file_url
        $this->get("api/v1/tasks/$task->id/file_url")
            ->assertStatus(Response::HTTP_FORBIDDEN)
            ->assertJsonStructure(
                [
                    'error',
                    'message',
                ]
            );
    }
}