import Echo from 'laravel-echo';

import Pusher from 'pusher-js';

window.Pusher = Pusher;

window.Echo = new Echo({
    broadcaster: 'reverb',
    key: import.meta.env.VITE_REVERB_APP_KEY,
    wsHost: import.meta.env.VITE_REVERB_HOST,
    wsPort: import.meta.env.VITE_REVERB_PORT ?? 80,
    wssPort: import.meta.env.VITE_REVERB_PORT ?? 443,
    forceTLS: (import.meta.env.VITE_REVERB_SCHEME ?? 'https') === 'https',
    enabledTransports: ['ws', 'wss'],
});

document.addEventListener('livewire:init', () => {
    let doneTypingInterval = 1000;
    let typingTimer, receiverId, senderId;

    Livewire.on('fetchIds', function (e) {
        receiverId = e.receiverId;
        senderId = e.senderId;
    });

    document.getElementById('oneTimeMessage').addEventListener('input', function (e) {
        clearTimeout(typingTimer);
        typingTimer = setTimeout(doneTyping, doneTypingInterval);

        if (this.value !== "") {
            window.Echo.private(`chat.${receiverId}`).whisper("typing", {
                receiverId: receiverId,
                senderId: senderId
            });
        }
    });

    function doneTyping() {
        window.Echo.private(`chat.${receiverId}`).whisper("stopped-typing", {});
    }
});
