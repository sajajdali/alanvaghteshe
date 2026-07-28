import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Pusher = Pusher;
window.Echo = new Echo({
    broadcaster: 'pusher',
    key: 'fa4fe3540c4368b08b57',
    cluster: 'mt1',
    forceTLS: true
});
