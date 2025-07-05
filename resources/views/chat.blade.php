use LucianoTonet\GroqLaravel\Facades\Groq;

$response = Groq::chat()->completions()->create([
    'model' => 'meta-llama/llama-4-maverick-17b-128e-instruct', // Or any other model
    'messages' => [
        ['role' => 'user', 'content' => 'Hello, how are you?']
    ]
]);

echo $response['choices'][0]['message']['content'];
