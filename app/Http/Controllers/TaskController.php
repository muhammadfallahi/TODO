<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // TODO: show deleted task
        $tasks = auth()->user()->tasks;
        //$tasks = Task::withTrashed()->get();

        return view('tasks.index')
            ->with('tasks', $tasks);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $users = User::all(['id', 'name']);

        /*return view('tasks.create', [
            'users' => $users
        ]);*/

        return view('tasks.create', compact('users'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|integer|exists:users,id',
            'title' => 'required|min:10',
            'description' => 'required'
        ]);

        // $task = Task::create($request->all());
        $task = Task::create($validated);

        /*$task = new Task();
        $task->user_id = $request->get('user_id');
        $task->title = $request->title;
        $task->save();*/

        return redirect()->route('tasks.show', $task);
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $task = Task::withTrashed()->findOrFail($id);

        return view('tasks.show', compact('task'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Task $task)
    {
        $task->update([
            'is_done' => !$task->is_done
        ]);

        return $task;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Task::find($id)->delete();

        return redirect()
            ->route('tasks.index');
    }

    public function forceDelete($id)
    {
        Task::withTrashed()->find($id)->forceDelete();

        return redirect()
            ->route('tasks.index');
    }

    public function restore($id)
    {
        Task::withTrashed()->find($id)->restore();

        return back();
    }

    public function done(Task $task)
    {
        /*$task->update([
            "is_done" => true
        ]);*/

        $task->update([
            "is_done" => !$task->is_done
        ]);

        return back();
    }
}
