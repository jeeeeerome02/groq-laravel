<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Laravel-Groq AI Chat | Developed by Jerome Evangelista</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

    <style>
        body {
            background: #f1f4f9;
            font-family: 'Segoe UI', sans-serif;
        }

        .chat-container {
            max-width: 768px;
            margin: auto;
            margin-top: 40px;
            height: 90vh;
            display: flex;
            flex-direction: column;
            border-radius: 16px;
            background: #ffffff;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            overflow: hidden;
        }

        .chat-header {
            background: #0d6efd;
            color: #fff;
            padding: 16px;
            text-align: center;
            font-weight: bold;
            font-size: 1.25rem;
        }

        .chat-box {
            flex: 1;
            overflow-y: auto;
            padding: 16px;
            background: #f9fafb;
        }

        .message {
            display: flex;
            margin-bottom: 12px;
            align-items: flex-end;
        }

        .message.user {
            justify-content: flex-end;
        }

        .message.bot {
            justify-content: flex-start;
        }

        .message .bubble {
            max-width: 70%;
            padding: 12px 16px;
            border-radius: 16px;
            position: relative;
            font-size: 0.95rem;
        }

        .message.user .bubble {
            background-color: #0d6efd;
            color: white;
            border-bottom-right-radius: 4px;
        }

        .message.bot .bubble {
            background-color: #e9ecef;
            color: #212529;
            border-bottom-left-radius: 4px;
        }

        .avatar {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background-color: #dee2e6;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 10px;
            font-size: 18px;
            color: #495057;
        }

        .message.user .avatar {
            margin-left: 10px;
            margin-right: 0;
            background-color: #0d6efd;
            color: white;
        }

        .chat-footer {
            padding: 12px 16px;
            border-top: 1px solid #e0e0e0;
        }

        .typing-indicator {
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }

        .typing-indicator span {
            width: 8px;
            height: 8px;
            background-color: #adb5bd;
            border-radius: 50%;
            animation: bounce 1.2s infinite;
        }

        .typing-indicator span:nth-child(2) {
            animation-delay: 0.2s;
        }

        .typing-indicator span:nth-child(3) {
            animation-delay: 0.4s;
        }

        @keyframes bounce {
            0%, 80%, 100% {
                transform: scale(0);
                opacity: 0.4;
            }
            40% {
                transform: scale(1);
                opacity: 1;
            }
        }

        footer {
            text-align: center;
            font-size: 0.85rem;
            color: #6c757d;
            padding: 10px 0;
        }

        @media (max-width: 576px) {
            .chat-container {
                margin: 10px;
                height: 92vh;
            }

            .bubble {
                max-width: 90%;
            }
        }
    </style>
</head>
<body>

<div class="chat-container">
    <div class="chat-header">
        Groq AI Chat <br>
        <small style="font-size: 0.75rem; color: #e2e6ea;">Developed by Jerome Evangelista</small>
    </div>

    <div class="chat-box" id="chatBox">
        <!-- Messages here -->
    </div>

    <div class="chat-footer">
        <form id="chatForm" class="d-flex gap-2">
            <input type="text" name="message" id="messageInput" class="form-control" placeholder="Ask something..." required>
            <button class="btn btn-primary" type="submit"><i class="bi bi-send"></i></button>
        </form>
    </div>
</div>

<footer>&copy; <span id="year"></span> Jerome Evangelista</footer>

<script>
    const chatBox = document.getElementById('chatBox');
    const chatForm = document.getElementById('chatForm');
    const messageInput = document.getElementById('messageInput');
    document.getElementById('year').textContent = new Date().getFullYear();

    let conversationHistory = [
        { role: 'assistant', content: 'Hi, I am Groq AI Chatbot developed by Jerome Evangelista, How can I help you?' }
    ];

    // Show greeting
    window.addEventListener('DOMContentLoaded', () => {
        addMessage(conversationHistory[0].content, 'bot');
    });

    chatForm.addEventListener('submit', async function (e) {
        e.preventDefault();

        const userMessage = messageInput.value;
        addMessage(userMessage, 'user');
        conversationHistory.push({ role: 'user', content: userMessage });
        messageInput.value = '';

        // Typing indicator
        const typingIndicator = document.createElement('div');
        typingIndicator.className = 'message bot';
        typingIndicator.innerHTML = `
            <div class="avatar"><i class="bi bi-robot"></i></div>
            <div class="bubble">
                <div class="typing-indicator">
                    <span></span><span></span><span></span>
                </div>
            </div>`;
        chatBox.appendChild(typingIndicator);
        scrollToBottom();

        try {
            const response = await fetch('/chat/send', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ history: conversationHistory })
            });

            const data = await response.json();
            typingIndicator.remove();
            addMessage(data.reply, 'bot');
            conversationHistory.push({ role: 'assistant', content: data.reply });
        } catch (error) {
            typingIndicator.remove();
            addMessage("Something went wrong. Please try again.", 'bot');
        }
    });

    function addMessage(content, sender) {
        const messageDiv = document.createElement('div');
        messageDiv.className = `message ${sender}`;

        const avatar = document.createElement('div');
        avatar.className = 'avatar';
        avatar.innerHTML = sender === 'user' ? '<i class="bi bi-person-fill"></i>' : '<i class="bi bi-robot"></i>';

        const bubble = document.createElement('div');
        bubble.className = 'bubble';
        bubble.textContent = content;

        if (sender === 'user') {
            messageDiv.appendChild(bubble);
            messageDiv.appendChild(avatar);
        } else {
            messageDiv.appendChild(avatar);
            messageDiv.appendChild(bubble);
        }

        chatBox.appendChild(messageDiv);
        scrollToBottom();
    }

    function scrollToBottom() {
        chatBox.scrollTop = chatBox.scrollHeight;
    }
</script>

</body>
</html>
