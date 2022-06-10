<?php

namespace App\Http\Controllers\V1;

use App\Exceptions\APIException;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\V1\Task;
use App\Models\V1\Tags;
use Exception;
use Throwable;

class TaskController extends Controller
{
    const ERROR_MESSAGE_PT_BR = [
            'name' => 'O nome da tarefa é obrigatório',
            'status' => 'O status da tarefa é obrigatório',
            'file_url' => 'A url da tarefa é obrigatória',
            'file_url_invalid' => 'Url inválida',
            'not_found' => 'Nenhuma tarefa encontrada',
            'status_invalid' => 'Status inválido'
        ],
        STATUS_ORDER = [
            'BACKLOG',
            'IN_PROGRESS',
            'WAITING_CUSTOMER_APPROVAL',
            'APPROVED'
        ];

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $tasks = Task::with('tags')
            ->get()
            ->makeHidden('file_url');

        if ($tasks->isEmpty())
            throw new APIException(new Exception(self::ERROR_MESSAGE_PT_BR['not_found']), 204);
        else
            return $tasks;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required',
                'file_url' => 'required|url'
            ], [
                'name.required' => self::ERROR_MESSAGE_PT_BR['name'],
                'file_url.required' => self::ERROR_MESSAGE_PT_BR['file_url'],
                'file_url.url' => self::ERROR_MESSAGE_PT_BR['file_url_invalid'],
            ]);

            return Task::create($request->all());
        } catch (Throwable $e) {
            throw new APIException($e, 400);
        }
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        try {
            // get task
            $task = Task::findOrFail($id);

            // validation
            $request->validate([
                'name' => 'required',
                'status' => 'required|in:' . implode(',', self::STATUS_ORDER),
                'file_url' => 'required|url'
            ], [
                'name.required' => self::ERROR_MESSAGE_PT_BR['name'],
                'status.required' => self::ERROR_MESSAGE_PT_BR['status'],
                'status.in' => self::ERROR_MESSAGE_PT_BR['status_invalid'],
                'file_url.required' => self::ERROR_MESSAGE_PT_BR['file_url'],
                'file_url.url' => self::ERROR_MESSAGE_PT_BR['file_url_invalid'],
            ]);

            // check valid status order
            $this->checkOrderStatus($task->status, $request->input('status'));

            // save
            $task->update($request->all());

            return Response()->noContent();
        } catch (Throwable $e) {
            throw new APIException($e, 400);
        }
    }

    /**
     * Update the specified status in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update_status(Request $request, $id)
    {
        try {
            // get task
            $task = Task::findOrFail($id);

            // validation
            $request->validate([
                'status' => 'required|in:' . implode(',', self::STATUS_ORDER),
            ], [
                'status.required' => self::ERROR_MESSAGE_PT_BR['status'],
                'status.in' => self::ERROR_MESSAGE_PT_BR['status_invalid'],
            ]);

            // check valid status order
            $this->checkOrderStatus($task->status, $request->input('status'));

            // save
            $task->update($request->all());

            return Response()->noContent();
        } catch (Throwable $e) {
            throw new APIException($e, 400);
        }
    }

    /**
     * 
     * @return void
     */
    private function checkOrderStatus(string $currentStatus, string $newStatus): void
    {
        $currentStatusLevel = array_search($currentStatus, self::STATUS_ORDER);
        $newStatusLevel = array_search($newStatus, self::STATUS_ORDER);

        // not allowed regress
        if ($newStatusLevel < $currentStatusLevel)
            throw new Exception("O status da tarefa não pode ser regredido");

        // not allowed jump step
        if ($newStatusLevel > $currentStatusLevel + 1)
            throw new Exception("O status da tarefa não pode ser alterado. Ainda existem pendências");
    }

    /**
     * Display a file_url of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show_file_url(Request $request, $id)
    {
        try {
            $task = Task::findOrFail($id);

            if ($task->status !== 'APPROVED')
                throw new Exception("O link só estará disponível após a aprovação da tarefa", 403);

            return json_encode(['file_url' => $task->file_url]);
        } catch (Throwable $e) {
            throw new APIException($e, $e->getCode());
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store_tag(Request $request, $id)
    {
        try {
            // get tags registreds
            $tags = Task::with('tags')
                ->where('id', $id)
                ->get()
                ->first()
                ->tags
                ->toArray();

            // validation
            $request->validate([
                'tag_name' => 'required',
            ], [
                'tag_name.required' => 'O nome da tag é obrigatório',
            ]);

            // check if exist tag_name to this task
            if (array_search($request->input('tag_name'), array_column($tags, 'tag_name')) > -1)
                throw new Exception('Essa tag já existe para esta tarefa');

            // save
            Tags::create(array_merge($request->all(), ['task_id' => $id]));

            return Response()->noContent();
        } catch (Throwable $e) {
            throw new APIException($e, 400);
        }
    }
}
