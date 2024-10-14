<?php

namespace App\Http\Controllers;

use App\Events\TaskCreate;
use App\Events\TaskDelete;
use App\Events\TaskUpdate;
use App\Http\Requests\TaskStoreRequest;
use App\Models\Task;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class TaskController extends Controller
{
    //Create Method
    public function store(TaskStoreRequest $request)
    {
        try {
            $task = Task::create([
                'title' => $request->title,
                'description' => $request->description,
                'status' => $request->status,
                'user_id' => Auth::id()
            ]);
            broadcast(new TaskCreate($task))->toOthers();
            return response()->json([
                'message' => 'Task created successfully.',
                'task' => $task
            ], 201);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Failed to Create Task.',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    // All Tasks
    public function index()
    {
        $tasks = Task::where('user_id', auth()->id())->get();
        return response()->json($tasks);
    }

    //Update method
    public function update(Request $request, $id)
    {
        try {
            $task = Task::find($id);

            $task->update($request->only([
                'title',
                'description',
                'status'
            ]));
            broadcast(new TaskUpdate($task))->toOthers();
            return response()->json([
                'message' => 'Task Update successfully.',
                'task' => $task
            ], 201);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Failed to Update Task.',
                'error' => $e->getMessage(), // Optional: remove in production
            ], 500);
        }
    }

    // Delete method
    public function destroy($id)
    {
        try {
            $task = Task::find($id);
            $task->delete();
            broadcast(new TaskDelete($task))->toOthers();
            return response()->json([
                'message' => 'Task Delete successfully.',
                'task' => $task
            ], 201);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Failed to Delete Task.',
                'error' => $e->getMessage(), // Optional: remove in production
            ], 500);
        }
    }
}
