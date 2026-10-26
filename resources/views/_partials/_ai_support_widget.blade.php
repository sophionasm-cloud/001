<!-- Floating AI Support Agent Widget -->
<div id="ai-support-container" class="ai-widget-wrapper" style="position: fixed; bottom: 25px; right: 25px; z-index: 99999; font-family: 'Public Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;">

  <!-- Floating Toggle Button -->
  <button id="ai-support-toggle" type="button" aria-label="Open AI Assistant"
          style="width: 60px; height: 60px; border-radius: 50%; background: linear-gradient(135deg, #7367f0 0%, #a07bf7 100%); border: none; box-shadow: 0 8px 24px rgba(115, 103, 240, 0.45); cursor: pointer; display: flex; align-items: center; justify-content: center; position: relative; transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1); outline: none;">
    <span class="ai-pulse-ring" style="position: absolute; inset: -4px; border-radius: 50%; border: 2px solid rgba(115, 103, 240, 0.6); animation: aiPulse 2s infinite;"></span>
    <span id="ai-icon-chat" style="color: #ffffff; display: flex; align-items: center; justify-content: center;">
      <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <path d="M12 2a8 8 0 0 0-8 8c0 1.9.6 3.7 1.7 5.1L4 21l6.1-1.6c.6.2 1.3.3 1.9.3 4.4 0 8-3.6 8-8s-3.6-8-8-8z"></path>
        <path d="M9.5 9h.01"></path>
        <path d="M14.5 9h.01"></path>
        <path d="M9.5 13a3.5 3.5 0 0 0 5 0"></path>
      </svg>
    </span>
    <span id="ai-icon-close" style="color: #ffffff; display: none; align-items: center; justify-content: center;">
      <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <line x1="18" y1="6" x2="6" y2="18"></line>
        <line x1="6" y1="6" x2="18" y2="18"></line>
      </svg>
    </span>
    <!-- Badge -->
    <span id="ai-unread-badge" style="position: absolute; top: -2px; right: -2px; background: #ea5455; color: #fff; font-size: 10px; font-weight: 700; width: 20px; height: 20px; border-radius: 50%; display: flex; align-items: center; justify-content: center; border: 2px solid #fff; box-shadow: 0 2px 5px rgba(0,0,0,0.2);">AI</span>
  </button>

  <!-- Chat Window -->
  <div id="ai-support-window" style="display: none; width: 380px; max-width: calc(100vw - 32px); height: 560px; max-height: calc(100vh - 110px); background: #ffffff; border-radius: 18px; box-shadow: 0 16px 48px rgba(34, 41, 47, 0.22); overflow: hidden; flex-direction: column; position: absolute; bottom: 74px; right: 0; border: 1px solid rgba(115, 103, 240, 0.15); transition: opacity 0.25s ease, transform 0.25s ease; transform: translateY(10px) scale(0.97); opacity: 0;">

    <!-- Header -->
    <div style="background: linear-gradient(135deg, #7367f0 0%, #685dd8 100%); color: #ffffff; padding: 16px; display: flex; align-items: center; justify-content: space-between; position: relative;">
      <div style="display: flex; align-items: center; gap: 12px;">
        <div style="position: relative; width: 42px; height: 42px; background: rgba(255, 255, 255, 0.2); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 20px; border: 1.5px solid rgba(255, 255, 255, 0.35);">
          🤖
          <span style="position: absolute; bottom: 0; right: 0; width: 11px; height: 11px; background: #28c76f; border-radius: 50%; border: 2px solid #fff;"></span>
        </div>
        <div>
          <div style="font-weight: 700; font-size: 15px; letter-spacing: -0.2px; display: flex; align-items: center; gap: 6px;">
            Nova AI Support
            <span style="font-size: 9px; background: rgba(255, 255, 255, 0.25); padding: 2px 6px; border-radius: 10px; font-weight: 600; text-transform: uppercase;">Gemini</span>
          </div>
          <div style="font-size: 11px; opacity: 0.9; display: flex; align-items: center; gap: 4px;">
            <span style="display: inline-block; width: 6px; height: 6px; background: #28c76f; border-radius: 50%;"></span>
            Online • 24/7 MultiVendor Agent
          </div>
        </div>
      </div>
      <div style="display: flex; align-items: center; gap: 6px;">
        <!-- Clear History Button -->
        <button id="ai-clear-chat" title="Clear Conversation" style="background: rgba(255, 255, 255, 0.15); border: none; color: #fff; width: 30px; height: 30px; border-radius: 8px; cursor: pointer; display: flex; align-items: center; justify-content: center; transition: background 0.2s;">
          <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <polyline points="3 6 5 6 21 6"></polyline>
            <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
          </svg>
        </button>
        <!-- Minimize Button -->
        <button id="ai-minimize-chat" title="Minimize" style="background: rgba(255, 255, 255, 0.15); border: none; color: #fff; width: 30px; height: 30px; border-radius: 8px; cursor: pointer; display: flex; align-items: center; justify-content: center; transition: background 0.2s;">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <polyline points="6 9 12 15 18 9"></polyline>
          </svg>
        </button>
      </div>
    </div>

    <!-- Messages Area -->
    <div id="ai-messages-container" style="flex: 1; padding: 16px; overflow-y: auto; background: #f8f7fa; display: flex; flex-direction: column; gap: 14px; scroll-behavior: smooth;">
      <!-- Welcome message injected dynamically via JS -->
    </div>

    <!-- Suggestions / Quick Action Chips -->
    <div id="ai-suggestions-container" style="padding: 6px 14px 10px 14px; background: #f8f7fa; display: flex; gap: 6px; overflow-x: auto; white-space: nowrap; scrollbar-width: none;">
      <!-- Chips injected dynamically via JS -->
    </div>

    <!-- Typing Indicator -->
    <div id="ai-typing-indicator" style="display: none; padding: 6px 16px 8px 16px; background: #f8f7fa;">
      <div style="display: inline-flex; align-items: center; gap: 6px; background: #ffffff; padding: 8px 14px; border-radius: 14px; box-shadow: 0 2px 8px rgba(0,0,0,0.06); border: 1px solid #ebe9f1;">
        <span style="font-size: 11px; color: #6e6b7b; font-weight: 500;">Nova is thinking</span>
        <span class="ai-dot" style="width: 5px; height: 5px; background: #7367f0; border-radius: 50%; display: inline-block; animation: aiDot 1.4s infinite ease-in-out both;"></span>
        <span class="ai-dot" style="width: 5px; height: 5px; background: #7367f0; border-radius: 50%; display: inline-block; animation: aiDot 1.4s infinite ease-in-out both; animation-delay: 0.2s;"></span>
        <span class="ai-dot" style="width: 5px; height: 5px; background: #7367f0; border-radius: 50%; display: inline-block; animation: aiDot 1.4s infinite ease-in-out both; animation-delay: 0.4s;"></span>
      </div>
    </div>

    <!-- Input Bar -->
    <div style="padding: 12px 14px; background: #ffffff; border-top: 1px solid #ebe9f1; display: flex; align-items: center; gap: 8px;">
      <input type="text" id="ai-user-input" placeholder="Ask about products, orders, vendors..."
             style="flex: 1; border: 1px solid #d8d6de; border-radius: 22px; padding: 10px 16px; font-size: 13px; outline: none; transition: border-color 0.2s; background: #fcfcfd;" />
      <button id="ai-send-btn" type="button" aria-label="Send Message"
              style="width: 40px; height: 40px; border-radius: 50%; background: #7367f0; border: none; color: #ffffff; display: flex; align-items: center; justify-content: center; cursor: pointer; transition: background 0.2s, transform 0.1s; flex-shrink: 0;">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-left: 2px;">
          <line x1="22" y1="2" x2="11" y2="13"></line>
          <polygon points="22 2 15 22 11 13 2 9 22 2"></polygon>
        </svg>
      </button>
    </div>

  </div>
</div>

<style>
@keyframes aiPulse {
  0% { transform: scale(1); opacity: 0.8; }
  50% { transform: scale(1.18); opacity: 0; }
  100% { transform: scale(1); opacity: 0; }
}
@keyframes aiDot {
  0%, 80%, 100% { transform: scale(0.6); opacity: 0.4; }
  40% { transform: scale(1.1); opacity: 1; }
}

.ai-chip-btn {
  background: #ffffff;
  border: 1px solid #dfdbee;
  color: #5d596c;
  padding: 5px 12px;
  border-radius: 16px;
  font-size: 11px;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.2s ease;
  flex-shrink: 0;
}
.ai-chip-btn:hover {
  background: #7367f0;
  color: #ffffff;
  border-color: #7367f0;
  transform: translateY(-1px);
}

.ai-bubble-user {
  background: #7367f0;
  color: #ffffff;
  padding: 10px 14px;
  border-radius: 16px 16px 4px 16px;
  font-size: 13px;
  line-height: 1.45;
  max-width: 82%;
  margin-left: auto;
  word-break: break-word;
  box-shadow: 0 2px 6px rgba(115, 103, 240, 0.25);
}

.ai-bubble-bot {
  background: #ffffff;
  color: #33303c;
  padding: 12px 14px;
  border-radius: 16px 16px 16px 4px;
  font-size: 13px;
  line-height: 1.5;
  max-width: 88%;
  margin-right: auto;
  word-break: break-word;
  border: 1px solid #ebe9f1;
  box-shadow: 0 2px 6px rgba(0, 0, 0, 0.04);
}
.ai-bubble-bot p { margin: 0 0 6px 0; }
.ai-bubble-bot p:last-child { margin-bottom: 0; }
.ai-bubble-bot a { color: #7367f0; font-weight: 600; text-decoration: underline; }
.ai-bubble-bot ul { margin: 4px 0; padding-left: 18px; }
.ai-bubble-bot li { margin-bottom: 3px; }

.ai-product-card {
  display: flex;
  align-items: center;
  gap: 10px;
  background: #f8f7fa;
  border: 1px solid #e7e5ec;
  border-radius: 10px;
  padding: 8px;
  margin-top: 8px;
  text-decoration: none !important;
  color: inherit !important;
  transition: border-color 0.2s, background 0.2s;
}
.ai-product-card:hover {
  background: #f1eff8;
  border-color: #7367f0;
}
.ai-product-card img {
  width: 44px;
  height: 44px;
  border-radius: 6px;
  object-fit: cover;
}
</style>

<script>
(function() {
  const toggleBtn = document.getElementById('ai-support-toggle');
  const chatWindow = document.getElementById('ai-support-window');
  const iconChat = document.getElementById('ai-icon-chat');
  const iconClose = document.getElementById('ai-icon-close');
  const unreadBadge = document.getElementById('ai-unread-badge');
  const minimizeBtn = document.getElementById('ai-minimize-chat');
  const clearBtn = document.getElementById('ai-clear-chat');
  const messagesContainer = document.getElementById('ai-messages-container');
  const suggestionsContainer = document.getElementById('ai-suggestions-container');
  const userInput = document.getElementById('ai-user-input');
  const sendBtn = document.getElementById('ai-send-btn');
  const typingIndicator = document.getElementById('ai-typing-indicator');

  let isOpen = false;
  let chatHistory = [];
  const STORAGE_KEY = 'nova_multivendor_chat_v1';

  // Toggle Chat
  function toggleChat(open) {
    if (typeof open === 'boolean') {
      isOpen = open;
    } else {
      isOpen = !isOpen;
    }

    if (isOpen) {
      chatWindow.style.display = 'flex';
      setTimeout(() => {
        chatWindow.style.opacity = '1';
        chatWindow.style.transform = 'translateY(0) scale(1)';
      }, 10);
      iconChat.style.display = 'none';
      iconClose.style.display = 'flex';
      unreadBadge.style.display = 'none';
      userInput.focus();
      scrollToBottom();
    } else {
      chatWindow.style.opacity = '0';
      chatWindow.style.transform = 'translateY(10px) scale(0.97)';
      setTimeout(() => {
        chatWindow.style.display = 'none';
      }, 250);
      iconChat.style.display = 'flex';
      iconClose.style.display = 'none';
    }
  }

  toggleBtn.addEventListener('click', toggleChat);
  minimizeBtn.addEventListener('click', () => toggleChat(false));

  // Clear Chat
  clearBtn.addEventListener('click', function() {
    if (confirm('Clear your chat history with Nova AI?')) {
      sessionStorage.removeItem(STORAGE_KEY);
      chatHistory = [];
      messagesContainer.innerHTML = '';
      initChat();
    }
  });

  // Markdown Parser
  function parseMarkdown(text) {
    if (!text) return '';
    let parsed = text
      .replace(/&/g, '&amp;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;');

    // Links [Title](URL)
    parsed = parsed.replace(/\[([^\]]+)\]\(([^)]+)\)/g, '<a href="$2" target="_blank" rel="noopener">$1</a>');
    // Bold **text**
    parsed = parsed.replace(/\*\*([^*]+)\*\*/g, '<strong>$1</strong>');
    // Italics *text*
    parsed = parsed.replace(/\*([^*]+)\*/g, '<em>$1</em>');
    // Bullet list items
    parsed = parsed.replace(/^[\s]*[•\-\*]\s+(.+)$/gm, '<li>$1</li>');
    parsed = parsed.replace(/(<li>.*<\/li>)/s, '<ul>$1</ul>');
    // Newlines to linebreaks
    parsed = parsed.replace(/\n\n/g, '<p></p>').replace(/\n/g, '<br>');

    return parsed;
  }

  // Append user message
  function appendUserMessage(text) {
    const msgDiv = document.createElement('div');
    msgDiv.className = 'ai-bubble-user';
    msgDiv.textContent = text;
    messagesContainer.appendChild(msgDiv);
    scrollToBottom();
  }

  // Append bot message with optional product cards
  function appendBotMessage(text, products = []) {
    const msgDiv = document.createElement('div');
    msgDiv.className = 'ai-bubble-bot';

    let contentHtml = '<div>' + parseMarkdown(text) + '</div>';

    // Render product recommendation cards
    if (products && products.length > 0) {
      contentHtml += '<div style="margin-top: 8px;">';
      products.forEach(p => {
        contentHtml += `
          <a href="${p.url}" class="ai-product-card" target="_blank">
            <img src="${p.image}" alt="${p.name}" onerror="this.src='/assets/img/products/1.png'">
            <div style="flex: 1; min-width: 0;">
              <div style="font-weight: 600; font-size: 12px; color: #5d596c; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">${p.name}</div>
              <div style="font-size: 11px; color: #7367f0; font-weight: 700;">$${p.price}</div>
              <div style="font-size: 10px; color: #a19fad;">By ${p.vendor}</div>
            </div>
            <span style="font-size: 11px; color: #7367f0; font-weight: 600;">View &rarr;</span>
          </a>
        `;
      });
      contentHtml += '</div>';
    }

    msgDiv.innerHTML = contentHtml;
    messagesContainer.appendChild(msgDiv);
    scrollToBottom();
  }

  function renderSuggestions(suggestions) {
    suggestionsContainer.innerHTML = '';
    if (!suggestions || suggestions.length === 0) return;

    suggestions.forEach(s => {
      const btn = document.createElement('button');
      btn.type = 'button';
      btn.className = 'ai-chip-btn';
      btn.textContent = s;
      btn.addEventListener('click', () => {
        userInput.value = s.replace(/^[^\w\s]+\s*/, ''); // strip leading emoji
        sendMessage();
      });
      suggestionsContainer.appendChild(btn);
    });
  }

  function scrollToBottom() {
    messagesContainer.scrollTop = messagesContainer.scrollHeight;
  }

  // Send Message
  async function sendMessage() {
    const text = userInput.value.trim();
    if (!text) return;

    userInput.value = '';
    appendUserMessage(text);
    chatHistory.push({ role: 'user', content: text });

    // Show typing indicator
    typingIndicator.style.display = 'block';
    scrollToBottom();

    try {
      const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

      const res = await fetch('/api/ai-chat', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': csrfToken,
          'Accept': 'application/json'
        },
        body: JSON.stringify({
          message: text,
          history: chatHistory.slice(-8)
        })
      });

      const data = await res.json();
      typingIndicator.style.display = 'none';

      if (data.status === 'success' && data.reply) {
        appendBotMessage(data.reply, data.products || []);
        chatHistory.push({ role: 'assistant', content: data.reply });
        sessionStorage.setItem(STORAGE_KEY, JSON.stringify(chatHistory));
        if (data.suggestions) {
          renderSuggestions(data.suggestions);
        }
      } else {
        appendBotMessage("I apologize, I couldn't process that request right now. You can browse our catalog at [Products Catalog](/products).");
      }
    } catch (err) {
      typingIndicator.style.display = 'none';
      appendBotMessage("⚠️ Connection timeout. For immediate support, please explore our [Help & Products](/products) or try again in a moment.");
    }
  }

  sendBtn.addEventListener('click', sendMessage);
  userInput.addEventListener('keydown', function(e) {
    if (e.key === 'Enter') {
      e.preventDefault();
      sendMessage();
    }
  });

  // Init Chat from Session or Server
  async function initChat() {
    const saved = sessionStorage.getItem(STORAGE_KEY);
    if (saved) {
      try {
        chatHistory = JSON.parse(saved);
        if (Array.isArray(chatHistory) && chatHistory.length > 0) {
          chatHistory.forEach(msg => {
            if (msg.role === 'user') {
              appendUserMessage(msg.content);
            } else {
              appendBotMessage(msg.content);
            }
          });
          renderSuggestions([
            "🛍️ Explore Products",
            "📦 Track An Order",
            "🏪 Become a Vendor",
            "🚚 Shipping & Returns"
          ]);
          return;
        }
      } catch (e) {}
    }

    // Fetch welcome message from server
    try {
      const res = await fetch('/api/ai-chat/init');
      const data = await res.json();
      if (data.status === 'success') {
        appendBotMessage(data.greeting);
        renderSuggestions(data.suggestions);
      }
    } catch (e) {
      appendBotMessage("👋 Hello! I'm **Nova**, your MultiVendor AI Assistant. How can I assist you with shopping, vendor registration, or order tracking today?");
      renderSuggestions(["🛍️ Explore Products", "📦 Track An Order", "🏪 Become a Vendor"]);
    }
  }

  // Load chat on boot
  initChat();
})();
</script>
