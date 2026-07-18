let isFirstOpen = true;


document.addEventListener("DOMContentLoaded", function () {
    //Check if the page execution was triggered by a browser reload/refresh
    const navigationEntries = performance.getEntriesByType("navigation")[0];
    const isRefresh = navigationEntries && navigationEntries.type === "reload";

    if (isRefresh) {
        //Instantly clear history if the user manually reloaded the page
        sessionStorage.removeItem("swiftcare_chat_history");
    } else {
        //Restore previous messages
        loadChatHistory();
    }
});

function toggleChat() {
    const chatBox = document.getElementById('chat-box');
    if (chatBox.style.display === 'none' || chatBox.style.display === '') {
        chatBox.style.display = 'flex';
        
       
        const hasHistory = sessionStorage.getItem("swiftcare_chat_history");
        if (isFirstOpen && !hasHistory) {
            triggerIntroduction();
            isFirstOpen = false;
        }
    } else {
        chatBox.style.display = 'none';
    }
}

function triggerIntroduction() {
    //Falls back to "there" if the name fails to load
    let userName = typeof loggedInUser !== 'undefined' ? loggedInUser : "there";
    
    appendMessage("bot", `Hello ${userName}! Welcome to SwiftCare. I am SwiftBot, your virtual clinic assistant. 😊`);
    setTimeout(() => {
        appendMessage("bot", "What health concerns or symptoms are you experiencing today? I can help guide you to the right service or doctor.");
    }, 800);
}

function handleKeyPress(e) {
    if (e.key === 'Enter') sendMessage();
}

function sendMessage() {
    const input = document.getElementById('chat-input');
    const msg = input.value.trim();
    if (!msg) return;

    appendMessage("user", msg);
    input.value = '';

   
    fetch('../../controller/ChatbotController.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'message=' + encodeURIComponent(msg)
    })
    .then(response => response.json())
    .then(data => {
        setTimeout(() => { 
            appendMessage("bot", data.reply); 
        }, 500);
    })
    .catch(error => {
        console.error('Error:', error);
        setTimeout(() => {
            appendMessage("bot", "I can see your concern, but my local database router back-end logic hasn't been created yet!");
        }, 500);
    });
}

function appendMessage(sender, text) {
    const logs = document.getElementById('chat-logs');
    if (!logs) return; 

    const msgDiv = document.createElement('div');
    msgDiv.classList.add('chat-msg', sender);
    msgDiv.innerHTML = text;
    logs.appendChild(msgDiv);
    
    
    logs.scrollTop = logs.scrollHeight;

    
    saveChatHistory();
}


function saveChatHistory() {
    const logs = document.getElementById('chat-logs');
    if (logs) {
        sessionStorage.setItem("swiftcare_chat_history", logs.innerHTML);
    }
}

function loadChatHistory() {
    const savedMessages = sessionStorage.getItem("swiftcare_chat_history");
    const logs = document.getElementById('chat-logs');
    
    if (savedMessages && logs) {
        logs.innerHTML = savedMessages;
       
        isFirstOpen = false;
        
        
        setTimeout(() => {
            logs.scrollTop = logs.scrollHeight;
        }, 100);
    }
}