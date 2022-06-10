<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\V1\Task;
use App\Models\V1\Tags;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // TODO
        // remove file_url
        // add tags
        return Task::with('tags')->get();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'status' => 'required',
            'file_url'=> 'required'
        ]);

        return Task::create($request->all());
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
        $task = Task::findOrFail($id);
        $request->validate([
            'name' => 'required',
            'status' => 'required',
            'file_url' => 'required'
        ]);

        $task->update($request->all());
        return $task;
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
        // valid order
        $task = Task::findOrFail($id);
        $request->validate([
            'status' => 'required',
        ]);

        $task->update($request->all());
        return Response()->noContent();
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
        return Task::find($id)->file_url;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store_tag(Request $request, $id)
    {
        // check exist tag to task
        // return Task::find($id);
        $request->validate([
            'tag_name' => 'required',
            'task_id' => 'required'
        ]);

        Tags::create($request->all());
        return Response()->noContent();
    }
}
