<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Laravel-Groq AI Chat | Developed by Jerome Evangelista</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

    <style>
        :root {
            --primary: #0d6efd;
            --bg-light: #f1f4f9;
            --bg-dark: #1e1e1e;
            --text-light: #212529;
            --text-dark: #f8f9fa;
            --bot-bg: #e9ecef;
            --user-bg: #0d6efd;
        }

        body {
            background: var(--bg-light);
            font-family: 'Segoe UI', sans-serif;
            transition: background 0.3s ease;
        }

        @media (prefers-color-scheme: dark) {
            body {
                background: var(--bg-dark);
                color: var(--text-dark);
            }
            .chat-container {
                background: #2a2d30;
            }
            .chat-box {
                background: #1f1f1f;
            }
            .message.bot .bubble {
                background-color: #333;
                color: #f1f1f1;
            }
            .message.user .bubble {
                background-color: var(--primary);
                color: white;
            }
            .chat-header {
                background-color: #0d6efd;
            }
            .form-control {
                background-color: #333;
                color: white;
                border: 1px solid #555;
            }
        }

        .chat-container {
            max-width: 768px;
            margin: 40px auto;
            height: 90vh;
            display: flex;
            flex-direction: column;
            border-radius: 16px;
            background: #ffffff;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .chat-header {
            background: var(--primary);
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
            scroll-behavior: smooth;
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
            max-width: 75%;
            padding: 12px 16px;
            border-radius: 16px;
            position: relative;
            font-size: 0.95rem;
            word-wrap: break-word;
            white-space: pre-wrap;
        }

        .message.user .bubble {
            background-color: var(--user-bg);
            color: white;
            border-bottom-right-radius: 4px;
        }

        .message.bot .bubble {
            background-color: var(--bot-bg);
            color: var(--text-light);
            border-bottom-left-radius: 4px;
            position: relative;
        }

        .message.bot .copy-btn {
            position: absolute;
            bottom: 4px;
            right: 8px;
            font-size: 0.75rem;
            background: transparent;
            border: none;
            color: #6c757d;
            cursor: pointer;
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
            background-color: var(--primary);
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

        .typing-indicator span:nth-child(2) { animation-delay: 0.2s; }
        .typing-indicator span:nth-child(3) { animation-delay: 0.4s; }

        @keyframes bounce {
            0%, 80%, 100% { transform: scale(0); opacity: 0.4; }
            40% { transform: scale(1); opacity: 1; }
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

    <div class="chat-box" id="chatBox"></div>

    <div class="chat-footer">
        <form id="chatForm" class="d-flex gap-2">
            <input type="text" name="message" id="messageInput" class="form-control" placeholder="Ask something..." required autofocus>
            <button class="btn btn-primary" type="submit"><i class="bi bi-send"></i></button>
        </form>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<!-- Toast Container -->
<div class="position-fixed bottom-0 end-0 p-3" style="z-index: 9999">
    <div id="copyToast" class="toast align-items-center text-white bg-success border-0" role="alert">
        <div class="d-flex">
            <div class="toast-body">
                Message copied!
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
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

    // Show initial bot message
    window.addEventListener('DOMContentLoaded', () => {
        addMessage(conversationHistory[0].content, 'bot');
    });

    chatForm.addEventListener('submit', async function (e) {
        e.preventDefault();

        const userMessage = messageInput.value.trim();
        if (!userMessage) return;

        addMessage(userMessage, 'user');
        conversationHistory.push({ role: 'user', content: userMessage });
        messageInput.value = '';

        const typingIndicator = document.createElement('div');
        typingIndicator.className = 'message bot';
        typingIndicator.innerHTML = `
            <div class="avatar"><i class="bi bi-robot"></i></div>
            <div class="bubble"><div class="typing-indicator"><span></span><span></span><span></span></div></div>`;
        chatBox.appendChild(typingIndicator);
        scrollToBottom();

        try {
            const baseUrl = "{{ url('') }}";
            const response = await fetch(`${baseUrl}/chat/send`, {

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
    avatar.innerHTML = sender === 'user'
        ? '<i class="bi bi-person-fill"></i>'
        : '<i class="bi bi-robot"></i>';

    const bubble = document.createElement('div');
    bubble.className = 'bubble';

    if (sender === 'bot') {
        const span = document.createElement('span');
        bubble.appendChild(span);
        messageDiv.appendChild(avatar);
        messageDiv.appendChild(bubble);
        chatBox.appendChild(messageDiv);
        scrollToBottom();

        // Typing then show copy button
        typeMessage(content, span, () => {
            const copyBtn = document.createElement('button');
            copyBtn.innerHTML = '<i class="bi bi-clipboard"></i>';
            copyBtn.className = 'copy-btn';
            copyBtn.title = 'Copy';
            copyBtn.onclick = () => {
    navigator.clipboard.writeText(content).then(() => {
        const toast = new bootstrap.Toast(document.getElementById('copyToast'));
        toast.show();
    });
};

            bubble.appendChild(copyBtn);
        });
    } else {
        bubble.textContent = content;
        messageDiv.appendChild(bubble);
        messageDiv.appendChild(avatar);
        chatBox.appendChild(messageDiv);
        scrollToBottom();
    }
}


    function scrollToBottom() {
        chatBox.scrollTop = chatBox.scrollHeight;
    }
    function typeMessage(text, element, callback, delay = 20) {
    let i = 0;

    function type() {
        if (i < text.length) {
            element.textContent += text.charAt(i);
            i++;
            scrollToBottom();
            setTimeout(type, delay);
        } else if (typeof callback === 'function') {
            callback(); // Show copy button after typing
        }
    }

    type();
}


    // Enter to send, Shift+Enter for newline
    messageInput.addEventListener('keydown', function(e) {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            chatForm.requestSubmit();
        }
    });
</script>

</body>
</html>
