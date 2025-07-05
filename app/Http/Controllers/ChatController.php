<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use LucianoTonet\GroqLaravel\Facades\Groq;

class ChatController extends Controller
{
    public function chatWithGroq()
    {
        $response = Groq::chat()->completions()->create([
            'model' => 'meta-llama/llama-4-maverick-17b-128e-instruct',
            'messages' => [
                ['role' => 'user', 'content' => 'Hello, how are you?']
            ]
        ]);

        return $response['choices'][0]['message']['content'];
    }
    public function showChat()
{
    return view('chat');
}

public function sendMessage(Request $request)
{
    $message = $request->input('message');

    $response = \LucianoTonet\GroqLaravel\Facades\Groq::chat()->completions()->create([
        'model' => 'meta-llama/llama-4-maverick-17b-128e-instruct',
        'messages' => [
            ['role' => 'user', 'content' => $message]
        ]
    ]);

    return response()->json([
        'reply' => $response['choices'][0]['message']['content']
    ]);
}

}
