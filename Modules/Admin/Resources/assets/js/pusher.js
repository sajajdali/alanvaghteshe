import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Pusher = Pusher;
window.Echo = new Echo({
    broadcaster: 'pusher',
    key: process.env.MIX_PUSHER_APP_KEY,
    cluster: process.env.MIX_PUSHER_APP_CLUSTER || 'eu',
    wsHost: process.env.MIX_PUSHER_HOST || 'broadcast.shemiranweb.com',
    wsPort: Number(process.env.MIX_PUSHER_PORT || 443),
    wssPort: Number(process.env.MIX_PUSHER_PORT || 443),
    forceTLS: (process.env.MIX_PUSHER_SCHEME || 'https') === 'https',
    enabledTransports: ['ws', 'wss'],
    disableStats: true
});
