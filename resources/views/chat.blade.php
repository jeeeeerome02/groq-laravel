<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Laravel-Groq AI Chat | Developed by Jerome Evangelista</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

    <style>
        html {
            box-sizing: border-box;
        }
        *, *:before, *:after {
            box-sizing: inherit;
        }
        :root {
    --primary: #3d8bfd;
    --primary-light: #6ea8fe;
    --bg-light: #f7f9fb;
    --bg-dark: #23243a;
    --text-light: #23243a;
    --text-dark: #f0f0f0;
    --bot-bg: #f3f5f8;
    --user-bg: #e9f0fb;
    --glass-bg-light: rgba(255, 255, 255, 0.7);
    --glass-bg-dark: rgba(30, 30, 47, 0.6);
    --header-gradient: linear-gradient(90deg, #f7f9fb 0%, #e9f0fb 100%);
}

body {
    min-height: 100vh;
    background: linear-gradient(120deg, #f7f9fb 0%, #e9f0fb 100%);
    background-attachment: fixed;
    font-family: 'Segoe UI', sans-serif;
    transition: background 0.3s ease, color 0.3s ease;
    position: relative;
    overflow-x: hidden;
}

body::before {
    content: '';
    position: fixed;
    top: 0; left: 0; right: 0; bottom: 0;
    z-index: -1;
    background: radial-gradient(circle at 80% 10%, #3d8bfd11 0%, transparent 60%),
                radial-gradient(circle at 10% 90%, #6ea8fe11 0%, transparent 60%);
    animation: bgmove 12s linear infinite alternate;
}

@keyframes bgmove {
    0% { background-position: 80% 10%, 10% 90%; }
    100% { background-position: 70% 20%, 20% 80%; }
}

@media (prefers-color-scheme: dark) {
    body {
        background: linear-gradient(120deg, #23243a 0%, #1e1e2f 100%);
        color: var(--text-dark);
    }
    body::before {
        background: radial-gradient(circle at 80% 10%, #3d8bfd22 0%, transparent 60%),
                    radial-gradient(circle at 10% 90%, #6ea8fe22 0%, transparent 60%);
    }
    .chat-container {
        background: var(--glass-bg-dark);
        backdrop-filter: blur(10px);
    }
    .chat-box {
        background: transparent;
    }
    .message.bot .bubble {
        background: #23243a;
        color: var(--text-dark);
    }
    .message.user .bubble {
        background: #2e3956;
        color: #e9f0fb;
    }
    .form-control {
        background-color: #23243a;
        color: white;
        border: 1px solid #444;
    }
    .chat-header {
        background: var(--glass-bg-dark);
    }
}

.chat-container {
    max-width: 700px;
    margin: 24px auto;
    height: 88vh;
    display: flex;
    flex-direction: column;
    border-radius: 1rem;
    background: var(--glass-bg-light);
    backdrop-filter: blur(10px);
    box-shadow: 0 4px 18px rgba(61,139,253,0.06);
    overflow: hidden;
    border: 1px solid rgba(0,0,0,0.03);
    position: relative;
}

.chat-header {
    background: var(--header-gradient);
    color: #23243a;
    padding: 12px 12px 8px 12px;
    text-align: center;
    font-weight: 600;
    font-size: 1.1rem;
    letter-spacing: 0.2px;
    position: sticky;
    top: 0;
    z-index: 2;
    box-shadow: none;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.2rem;
}

.header-logo {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    background: rgba(61,139,253,0.08);
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 0.2rem;
    box-shadow: none;
    font-size: 1.2rem;
}

.chat-header h5 {
    font-size: 1.05rem;
    color: #23243a;
    margin-bottom: 0.1rem;
    font-weight: 600;
}

.chat-header small {
    font-size: 0.78rem;
    color: #6c757d;
}

.chat-box {
    flex: 1;
    overflow-y: auto;
    padding: 14px 10px 10px 10px;
    background: transparent;
    scroll-behavior: smooth;
    display: flex;
    flex-direction: column;
    gap: 0.2rem;
}

.message {
    display: flex;
    margin-bottom: 8px;
    align-items: flex-end;
    opacity: 0;
    transform: translateY(10px);
    animation: fadeInUp 0.25s cubic-bezier(.23,1.02,.32,1) forwards;
}

@keyframes fadeInUp {
    to {
        opacity: 1;
        transform: none;
    }
}

.message.user {
    justify-content: flex-end;
}

.message.bot {
    justify-content: flex-start;
}

.message .bubble {
    max-width: 75%;
    padding: 10px 14px;
    border-radius: 14px;
    font-size: 0.98rem;
    line-height: 1.5;
    word-wrap: break-word;
    white-space: pre-wrap;
    position: relative;
    box-shadow: 0 2px 6px rgba(61,139,253,0.04);
    transition: box-shadow 0.2s;
    background: var(--bot-bg);
    color: var(--text-light);
    border-bottom-left-radius: 7px;
    border-bottom-right-radius: 14px;
}

.message.user .bubble {
    background: var(--user-bg);
    color: #23243a;
    border-bottom-right-radius: 7px;
    border-bottom-left-radius: 14px;
    box-shadow: 0 2px 6px rgba(61,139,253,0.06);
}

.message.bot .bubble {
    background: var(--bot-bg);
    color: var(--text-light);
    border-bottom-left-radius: 7px;
}

.message.bot .copy-btn {
    position: absolute;
    bottom: 4px;
    right: 8px;
    font-size: 0.8rem;
    background: transparent;
    border: none;
    color: #adb5bd;
    cursor: pointer;
    transition: color 0.2s;
    opacity: 0.7;
}
.message.bot .copy-btn:focus {
    outline: 2px solid var(--primary);
    outline-offset: 2px;
}
.message.bot .copy-btn:hover {
    color: var(--primary);
    opacity: 1;
}

.avatar {
    width: 28px;
    height: 28px;
    border-radius: 50%;
    background-color: #e9f0fb;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 7px;
    font-size: 1rem;
    color: #3d8bfd;
    box-shadow: none;
}

.message.user .avatar {
    margin-left: 7px;
    margin-right: 0;
    background: #3d8bfd;
    color: #fff;
}

.chat-footer {
    padding: 10px 10px;
    border-top: 1px solid rgba(0,0,0,0.03);
    background: transparent;
    display: flex;
    align-items: center;
    justify-content: center;
}

#chatForm {
    width: 100%;
    display: flex;
    gap: 0.4rem;
}

#messageInput {
    flex: 1;
    border-radius: 1.5rem;
    padding: 8px 14px;
    font-size: 0.98rem;
    border: 1px solid #e3e7ec;
    box-shadow: none;
    transition: border 0.2s;
    background: #f7f9fb;
}
#messageInput:focus {
    border: 1px solid var(--primary);
    outline: none;
    box-shadow: none;
}

#sendBtn {
    border-radius: 50%;
    width: 36px;
    height: 36px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.1rem;
    background: transparent;
    color: var(--primary);
    border: 1.5px solid var(--primary);
    box-shadow: none;
    transition: background 0.2s, color 0.2s, border 0.2s, transform 0.1s;
}
#sendBtn:focus {
    outline: 2px solid var(--primary-light);
    outline-offset: 2px;
    background: #e9f0fb;
    color: var(--primary);
}
#sendBtn:hover {
    background: #e9f0fb;
    color: #23243a;
    border: 1.5px solid #6ea8fe;
    transform: scale(1.04);
}

#ttsToggle {
    border-radius: 50%;
    width: 32px;
    height: 32px;
    font-size: 1rem;
    background: transparent;
    color: var(--primary);
    border: 1.2px solid var(--primary);
    box-shadow: none;
    transition: background 0.2s, color 0.2s, border 0.2s;
    padding: 0;
}
#ttsToggle:focus {
    outline: 2px solid var(--primary-light);
    outline-offset: 2px;
    background: #e9f0fb;
    color: var(--primary);
}
#ttsToggle:hover {
    background: #e9f0fb;
    color: #23243a;
    border: 1.2px solid #6ea8fe;
}

.typing-indicator {
    display: inline-flex;
    align-items: center;
    gap: 3px;
}

.typing-indicator span {
    width: 7px;
    height: 7px;
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
    color: #adb5bd;
    padding: 8px 0 4px 0;
    background: transparent;
    letter-spacing: 0.1px;
}

@media (max-width: 576px) {
    html, body {
        width: 100vw;
        min-width: 0;
        overflow-x: hidden;
    }
    .chat-container {
        width: 100%;
        max-width: 100vw;
        margin: 0;
        padding: 2vw;
        height: calc(100dvh - 4vw);
        border-radius: 1.2rem;
        box-shadow: 0 6px 32px 0 rgba(61,139,253,0.10), 0 1.5px 6px 0 rgba(61,139,253,0.06);
        background: rgba(255,255,255,0.82);
        backdrop-filter: blur(14px) saturate(1.2);
        border: 1.5px solid rgba(61,139,253,0.10);
        position: relative;
        overflow-x: hidden;
        overflow-y: auto;
    }
    .chat-container::before {
        content: '';
        position: absolute;
        inset: 0;
        border-radius: 1.2rem;
        pointer-events: none;
        z-index: 1;
        background: linear-gradient(120deg, rgba(61,139,253,0.08) 0%, rgba(255,255,255,0.0) 100%);
        animation: borderGlow 3s linear infinite alternate;
    }
    @keyframes borderGlow {
        0% { box-shadow: 0 0 0 0 rgba(61,139,253,0.10); }
        100% { box-shadow: 0 0 12px 2px rgba(61,139,253,0.13); }
    }
    .chat-header {
        padding: 6px 2vw 4px 2vw;
        font-size: 0.98rem;
        background: rgba(255,255,255,0.7);
        backdrop-filter: blur(8px);
        border-top-left-radius: 1.2rem;
        border-top-right-radius: 1.2rem;
    }
    .chat-header h5 {
        font-size: 0.98rem;
    }
    .chat-header small {
        font-size: 0.7rem;
    }
    .chat-box {
        padding: 4vw 2vw 2vw 2vw;
        gap: 0.1rem;
    }
    .bubble {
        max-width: 100%;
        font-size: 0.93rem;
        padding: 8px 8px;
        word-break: break-word;
    }
    .avatar {
        width: 22px;
        height: 22px;
        font-size: 0.9rem;
        margin-right: 4px;
        margin-left: 4px;
    }
    .chat-footer {
        padding: 2vw 2vw;
        background: rgba(255,255,255,0.7);
        backdrop-filter: blur(8px);
        border-bottom-left-radius: 1.2rem;
        border-bottom-right-radius: 1.2rem;
    }
    #chatForm {
        gap: 0.2rem;
    }
    #messageInput {
        padding: 7px 8px;
        font-size: 0.93rem;
        border-radius: 1.2rem;
    }
    #sendBtn {
        width: 28px;
        height: 28px;
        font-size: 0.9rem;
        border-radius: 50%;
    }
    #ttsToggle {
        width: 22px;
        height: 22px;
        font-size: 0.8rem;
        border-radius: 50%;
    }
    #voiceSelect {
        font-size: 0.9rem;
        min-width: 80px;
        max-width: 120px;
    }
    .position-fixed.bottom-0.end-0.p-3 {
        right: 0 !important;
        left: 0 !important;
        width: 100vw;
        padding: 0.5rem !important;
    }
    footer {
        font-size: 0.75rem;
        padding: 4px 0 2px 0;
    }
}
    </style>
</head>
<body>

<div class="chat-container">
    <div class="chat-header text-center">
        <div>
            <h5 class="mb-1 fw-semibold">Laravel-Groq AI Chat</h5>
            <small class="text-light-emphasis">Developed by Jerome Evangelista</small>
        </div>
        <div class="d-flex flex-wrap justify-content-center align-items-center gap-2 mt-2">
            <select id="voiceSelect" class="form-select form-select-sm" style="max-width: 160px;">
                <option value="auto">🌍 Auto Language</option>
            </select>
            <button id="ttsToggle" class="btn" title="Toggle Voice">
                <i id="ttsIcon" class="bi"></i>
            </button>
        </div>
    </div>

    <!-- Chat Box -->
    <div class="chat-box" id="chatBox"></div>
    <!-- Chat Footer -->
    <div class="chat-footer">
    <form id="chatForm" class="d-flex gap-2">
        <input type="text" name="message" id="messageInput" class="form-control" placeholder="Ask something..." required autofocus autocomplete="off">
        <button id="sendBtn" class="btn" type="submit" aria-label="Send">
            <i id="sendIcon" class="bi bi-send"></i>
        </button>
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

        // Copy Button
        const copyBtn = document.createElement('button');
        copyBtn.innerHTML = '<i class="bi bi-clipboard"></i>';
        copyBtn.className = 'copy-btn me-3';
        copyBtn.title = 'Copy';
        copyBtn.onclick = () => {
            navigator.clipboard.writeText(content).then(() => {
                const toast = new bootstrap.Toast(document.getElementById('copyToast'));
                toast.show();
            });
        };

        // Speaker Button
        const speakBtn = document.createElement('button');
        speakBtn.innerHTML = '<i class="bi bi-volume-up-fill"></i>';
        speakBtn.className = 'copy-btn';
        speakBtn.title = 'Speak';
        speakBtn.onclick = () => speakText(content);

        // Add buttons first, then text
        bubble.appendChild(span);
        bubble.appendChild(copyBtn);
        bubble.appendChild(speakBtn);
        messageDiv.appendChild(avatar);
        messageDiv.appendChild(bubble);
        chatBox.appendChild(messageDiv);
        scrollToBottom();

        // Typing animation
        typeMessage(content, span, () => {
            if (!isMuted) {
                speakText(content);
                
            }
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

    function resetSendButton() {
        const sendIcon = document.getElementById('sendIcon');
        sendIcon.classList.remove('bi-stop-circle-fill');
        sendIcon.classList.add('bi-send');
        document.getElementById('stopTypingBtn').classList.add('d-none');
    }
    //Typing message
    let typingStopped = false;

function typeMessage(text, element, callback, delay = 20) {
    let i = 0;
    typingStopped = false;

    // Swap icon to stop
    const sendIcon = document.getElementById('sendIcon');
    sendIcon.classList.remove('bi-send');
    sendIcon.classList.add('bi-stop-circle-fill');

    function type() {
        if (typingStopped) {
            element.textContent += '...';
            resetSendButton();
            return;
        }

        if (i < text.length) {
            element.textContent += text.charAt(i);
            i++;
            scrollToBottom();
            setTimeout(type, delay);
        } else {
            resetSendButton();
            if (typeof callback === 'function') {
                callback();
            }
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
    function speakText(text) {
    if (!window.speechSynthesis || isMuted) return;

    const utterance = new SpeechSynthesisUtterance(text);
    const voices = speechSynthesis.getVoices();

    // Use selected language from dropdown
    if (selectedLang !== 'auto') {
        const matchedVoice = voices.find(v => v.lang === selectedLang);
        if (matchedVoice) {
            utterance.voice = matchedVoice;
            utterance.lang = matchedVoice.lang;
        }
    }

    utterance.rate = 1;
    utterance.pitch = 1;
    utterance.volume = 1;

    window.speechSynthesis.cancel(); // Optional
    window.speechSynthesis.speak(utterance);
}
    //Mute toggle
    let isMuted = localStorage.getItem('groq-tts-muted') === 'true';
    let selectedLang = localStorage.getItem('groq-tts-lang') || 'auto';

    const voiceSelect = document.getElementById('voiceSelect');
    const ttsToggle = document.getElementById('ttsToggle');
    const ttsIcon = document.getElementById('ttsIcon');

    // Update the icon dynamically
    function updateTTSIcon() {
        if (isMuted) {
            ttsIcon.className = 'bi bi-volume-mute-fill';
            ttsToggle.classList.remove('btn-outline-success');
            ttsToggle.classList.add('btn-outline-secondary');
        } else {
            ttsIcon.className = 'bi bi-volume-up-fill';
            ttsToggle.classList.remove('btn-outline-secondary');
            ttsToggle.classList.add('btn-outline-success');
        }
    }

    // Toggle mute
    ttsToggle.addEventListener('click', () => {
        isMuted = !isMuted;
        localStorage.setItem('groq-tts-muted', isMuted);
        updateTTSIcon();
        window.speechSynthesis.cancel(); // stop ongoing speech immediately
    });

    // Save selected language
    voiceSelect.addEventListener('change', () => {
        selectedLang = voiceSelect.value;
        localStorage.setItem('groq-tts-lang', selectedLang);
    });

    // Populate voice dropdown
    function populateVoices() {
        const voices = speechSynthesis.getVoices();
        voiceSelect.innerHTML = '<option value="auto">🌍 Auto Language</option>';

        voices.forEach(voice => {
            const option = document.createElement('option');
            option.value = voice.lang;
            option.textContent = `${voice.name} (${voice.lang})`;
            if (voice.lang === selectedLang) {
                option.selected = true;
            }
            voiceSelect.appendChild(option);
        });
    }

    // Run once + on load
    populateVoices();
    if (typeof speechSynthesis !== 'undefined') {
        speechSynthesis.onvoiceschanged = populateVoices;
    }
    // Initial icon update on load
    updateTTSIcon();
    //DOM for typing
    document.getElementById('sendBtn').addEventListener('click', function () {
    if (document.getElementById('sendIcon').classList.contains('bi-stop-circle-fill')) {
        typingStopped = true;
        window.speechSynthesis.cancel();
    }
});

</script>

</body>
</html>
