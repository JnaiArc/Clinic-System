<link rel="stylesheet" href="/clinic1/view/css/chatbot.css">

<div id="chatbot-container">
<button id="chat-circle" class="btn btn-primary" onclick="toggleChat()">
    💬 Chat with SwiftBot
</button>


<div id="chat-box" class="chat-box" style="display: none;">
    <div class="chat-box-header">
        <strong>SwiftCare Assistant</strong>
        <span class="chat-box-toggle" onclick="toggleChat()">&times;</span>
    </div>
    <div id="chat-logs" class="chat-logs">
        
    </div>
    <div class="chat-input-area">
        <input type="text" id="chat-input" placeholder="Type your symptoms or concern..." onkeypress="handleKeyPress(event)"/>
        <button id="chat-submit" onclick="sendMessage()">Send</button>
    </div>
</div>

<script src="/clinic1/view/js/chatbot.js" defer></script>