<?php

namespace App\Http\Controllers;

use App\Repositories\ChatRepository;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    public function open() {
        try {
            return response()->json(ChatRepository::open(),200);
        }
        catch(\Exception $e) { return response()->json(["error" => $e->getMessage()], 400); };
    }

    public function chat(Request $request){
        try {
            $responseChat = ChatRepository::chat($request->message, $request->sessionToken, $request->sessionId);
        }
        catch(\Exception $e) { return response()->json(["error" => $e->getMessage()], 400); };

        return response()->json($responseChat,200);
    }

    public function history(Request $request) {
        try {
            return response()->json(ChatRepository::history($request->sessionToken),200);
        }
        catch(\Exception $e) { return response()->json(["error" => $e->getMessage()], 400); };
    }

    public function variables(Request $request) {
        try {
            return response()->json(ChatRepository::variables($request->sessionToken),200);
        }
        catch(\Exception $e) { return response()->json(["error" => $e->getMessage()], 400); };
    }
}
