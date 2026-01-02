import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

// Pusher masih diperlukan karena Reverb menggunakan Pusher protocol
window.Pusher = Pusher;

// Auto-detect environment
const isProduction = window.location.hostname !== 'localhost' && window.location.hostname !== '127.0.0.1';
const wsHost = isProduction ? window.location.hostname : (import.meta.env.VITE_REVERB_HOST || 'localhost');
const wsPort = isProduction ? 443 : (import.meta.env.VITE_REVERB_PORT || 8080);
const forceTLS = isProduction || (import.meta.env.VITE_REVERB_SCHEME === 'https');

window.Echo = new Echo({
    broadcaster: 'reverb',
    key: import.meta.env.VITE_REVERB_APP_KEY,
    wsHost: wsHost,
    wsPort: wsPort,
    wssPort: wsPort,
    forceTLS: forceTLS,
    enabledTransports: ['ws', 'wss'],
    authEndpoint: window.location.origin + '/broadcasting/auth',
    auth: {
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'),
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
        }
    },
    authorizer: (channel, options) => {
        return {
            authorize: (socketId, callback) => {
                const authUrl = window.location.origin + '/broadcasting/auth';
                
                fetch(authUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'),
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    body: JSON.stringify({
                        socket_id: socketId,
                        channel_name: channel.name
                    }),
                    credentials: 'same-origin'
                })
                .then(response => {
                    if (!response.ok) {
                        return response.text().then(text => {
                            throw new Error('Auth failed: ' + response.status);
                        });
                    }
                    return response.json();
                })
                .then(data => {
                    callback(null, data);
                })
                .catch(error => {
                    callback(error, null);
                });
            }
        };
    }
});

