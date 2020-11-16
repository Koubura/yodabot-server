<?php

namespace App\Http\Controllers;

use App\Repositories\ChatRepository;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    public function open() {
        return response()->json(ChatRepository::open(),200);
    }

    public function chat(Request $request){
        try {
            $responseChat = ChatRepository::chat($request->message, $request->sessionToken, $request->sessionId);
        }
        catch(\Exception $e) { return response()->json(["error" => $e->getMessage()], 400); };

        return response()->json($responseChat,200);
    }

    public function close() {

    }
}
