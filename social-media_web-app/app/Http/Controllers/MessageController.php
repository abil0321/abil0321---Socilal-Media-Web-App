<?php

namespace App\Http\Controllers;

use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class MessageController extends Controller
{
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'sender_id' => 'required',
            'receiver_id' => 'required',
            'message_content' => 'required|string',
        ]);

        if ($validator->fails()) {
            return \response()->json([
                'status' => 400,
                'message' => $validator->errors(),
                'data' => $validator,
            ], 400);
        }
        $message = Message::create([
            'sender_id' => $request->input('sender_id'),
            'receiver_id' => $request->input('receiver_id'),
            'message_content' => $request->input('message_content'),
        ]);

        return response()->json([
            'status' => 200,
            'message' => 'Berhasil mengirim pesan',
            'data' => $message,
        ], 200);
    }

    public function show($id)
    {
        $message = Message::find($id);

        return response()->json([
            'status' => 200,
            'message' => 'Berhasil menampilkan pesan',
            'data' => $message,
        ], 200);
    }

    public function destroy($id)
    {
        Message::destroy($id);

        return response()->json([
            'status' => 200,
            'message' => 'Berhasil menghapus pesan',
        ], 200);
    }

    public function showByUser(int $user_id)
    {
        $message = Message::where('receiver_id', $user_id)->get();

        return response()->json([
            'status' => 200,
            'message' => 'Berhasil menampilkan pesan',
            'data' => $message,
        ]);
    }
}
