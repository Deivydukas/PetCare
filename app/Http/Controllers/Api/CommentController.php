<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Comment;
use App\Models\Pet;

class CommentController extends Controller
{
    /**
     * Display a listing of comments for a specific pet.
     */
    public function index($petId)
    {
        $comments = Comment::where('pet_id', $petId)
            ->whereNull('parent_id')
            ->with('replies')
            ->get();

        return response()->json($comments);
    }

    /**
     * Store a newly created comment for a specific pet.
     */
    public function store(Request $request)
    {
        {
        $validated = $request->validate([
            'pet_id' => 'required|exists:pets,id',
            'user_name' => 'required|string|max:255',
            'content' => 'required|string',
            'parent_id' => 'nullable|exists:comments,id',
        ]);
          $comment = Comment::create($validated);

         $isReply = isset($validated['parent_id']) && !empty($validated['parent_id']);

        return response()->json([
            'message' => $isReply
                ? 'Reply added successfully'
                : 'Comment added successfully',
            'data' => $comment,
        ], 201);
    }
    }
    public function show($id)
    {
        $comment = Comment::with('replies')->find($id);

        if (!$comment) {
            return response()->json(['error' => 'Comment not found'], 404);
        }   

        return response()->json($comment);
    }
    /**
     * Update a specific comment.
     */
    public function update(Request $request, $id)
    {
        $comment = Comment::find($id);

        if (!$comment) {
            return response()->json([
                'error' => 'Comment not found',
                'id' => $id
            ], 404);
        }

        $validated = $request->validate([
            'user_name' => 'sometimes|string|max:255',
            'content' => 'sometimes|string|max:1000',
        ]);

        $comment->update($validated);

        return response()->json([
            'message' => 'Comment updated successfully',
            'data' => $comment
        ]);
    }

    /**
     * Remove a specific comment.
     */
    public function destroy($id)
    {
        $comment = Comment::find($id);

        if (!$comment) {
            return response()->json([
                'error' => 'Comment not found',
                'id' => $id
            ], 404);
        }

        $comment->delete();

        return response()->json([
            'message' => 'Comment deleted successfully',
            'deleted_id' => $id
        ]);
    }
}
