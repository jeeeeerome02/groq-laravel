<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use LucianoTonet\GroqLaravel\Facades\Groq;

class ChatController extends Controller
{
    public function showChat()
    {
        return view('chat');
    }

    public function sendMessage(Request $request)
    {
        $history = $request->input('history', []);

        $response = Groq::chat()->completions()->create([
            'model' => 'meta-llama/llama-4-maverick-17b-128e-instruct',
            'messages' => $history
        ]);

        return response()->json([
            'reply' => $response['choices'][0]['message']['content']
        ]);
    }
}
