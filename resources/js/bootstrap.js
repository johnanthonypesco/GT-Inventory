import axios from 'axios';
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

// Attach Pusher to window
window.Pusher = Pusher;

// Initialize Laravel Echo
window.Echo = new Echo({
    broadcaster: "reverb",
    key: process.env.MIX_REVERB_APP_KEY,
    wsHost: process.env.MIX_REVERB_HOST || "127.0.0.1",
    wsPort: process.env.MIX_REVERB_PORT || 8080,
    wssPort: process.env.MIX_REVERB_PORT || 8080,
    forceTLS: false,
    disableStats: true,
});

window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
