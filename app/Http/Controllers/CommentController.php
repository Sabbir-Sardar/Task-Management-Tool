<?php

namespace App\Http\Controllers;

use App\Events\CommentCreate;
use App\Http\Requests\CommentStoreRequest;
use App\Models\Comment;
use Exception;
use Illuminate\Http\Request;
use App\Models\Task;

class CommentController extends Controller
{
    //All comment with the task
    public function index(Request $request, $taskId)
    {
        try {

            $comments = Comment::where('task_id', $taskId)->get();

            if ($comments->isEmpty()) {
                return response()->json([
                    'message' => 'No comment found'
                ], 404);
            }
            return response()->json($comments);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Failed to get Comments.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    // Comment create method
    public function store(CommentStoreRequest $request, $taskId)
    {
        try {
            $comment = Comment::create([
                'task_id' => $taskId,
                'comment' => $request->comment
            ]);
            broadcast(new CommentCreate($comment))->toOthers();
            return response()->json([
                'comment' => $comment
            ], 201);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Failed to Create Comment.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
