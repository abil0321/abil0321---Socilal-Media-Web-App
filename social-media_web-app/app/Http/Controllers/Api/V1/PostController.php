<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\Like;
use App\Models\Post;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;

class PostController extends Controller
{
    public function index()
    {
        $post = Post::with([
            // NOTE: User: WAJIB tambah 'id' (Primary Key tabel users) - butuh relasi penghubungnya
            'user:id,name,created_at',

            // NOTE: WAJIB tambah 'post_id' (Penghubung ke Post) & 'id' - butuh relasi penghubungnya
            'comments:post_id,content,user_id',
            'likes:post_id,user_id',
        ])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'status' => 200,
            'message' => 'sukses',
            'data' => $post,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $user = JWTAuth::parseToken()->authenticate();
        $validator = Validator::make($request->all(), [
            'content' => 'required|string|max:255',
            'image_url' => 'nullable',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->errors(),
            ], 400);
        }

        $post = Post::create([
            'user_id' => $user->id,
            'content' => $request->input('content'),
            'image_url' => $request->input('image_url'),
        ]);

        return response()->json([
            'status' => 200,
            'message' => 'Berhasil membuat post baru',
            'data' => $post,
        ], 201);
    }

    public function show($id)
    {
        $post = Post::with(['comments', 'likes'])->findOrFail($id);

        return response()->json([
            'status' => 200,
            'message' => 'Berhasil mendapatkan data',
            'data' => $post,
        ]);
    }

    public function update($id, Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'content' => 'nullable|string|max:255',
            'image_url' => 'nullable',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->errors(),
            ], 400);
        }

        $post = Post::findOrFail($id);
        $post->content = $request->input('content');
        $post->image_url = $request->input('image_url');

        $post->save();

        return response()->json([
            'status' => 200,
            'message' => 'Berhasil memperbarui data',
            'data' => $post,
        ]);
    }

    public function destroy($id)
    {
        Post::destroy($id);

        return response()->json([
            'status' => 200,
            'message' => 'Post berhasil dihapus',
        ]);
    }

    // SECTION: Comments
    public function commentPost(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();
        $validator = Validator::make($request->all(), [
            'post_id' => 'required',
            'content' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'message' => $validator->errors(),
            ], 400);
        }

        $comment = Comment::create([
            'user_id' => $user->id,
            'post_id' => $request->input('post_id'),
            'content' => $request->input('content'),
        ]);

        return response()->json([
            'status' => 200,
            'message' => 'Berhasil menambahkan komentar',
            'data' => $comment,
        ]);
    }

    public function RemoveCommentPost($id)
    {
        Comment::destroy($id);

        return response()->json([
            'status' => 200,
            'message' => 'Komentar berhasil dihapus',
        ]);
    }

    // SECTION: Likes
    public function likePost(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();
        $validator = Validator::make($request->all(), [
            'post_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'message' => $validator->errors(),
            ]);
        }

        // NOTE: Check if the user has already liked the post
        $likes = Like::where('user_id', $user->id)
            ->where('post_id', $request->input('post_id'))
            ->first();
        if (!$likes) {
            $likes = Like::create([
                'user_id' => $user->id,
                'post_id' => $request->input('post_id'),
            ]);
        }
        unset($likes->deleted_at);

        return response()->json([
            'status' => 200,
            'message' => 'Berhasil Like Post',
            'data' => $likes,
        ]);
    }

    public function unlikePost($id)
    {
        Like::destroy($id);

        return response()->json([
            'status' => 200,
            'message' => 'Like berhasil dihapus',
        ]);
    }
}
