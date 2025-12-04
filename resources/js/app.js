import './bootstrap';

// Importar Echo para Reverb
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';
import axios from 'axios';

// Hacer Pusher disponible globalmente para Laravel Echo
window.Pusher = Pusher;
window.axios = axios;


// window.Echo = new Echo({
//     broadcaster: 'reverb',
//     key: import.meta.env.VITE_REVERB_APP_KEY,
//     wsHost: import.meta.env.VITE_REVERB_HOST,
//     wsPort: import.meta.env.VITE_REVERB_PORT,
//     wssPort: import.meta.env.VITE_REVERB_PORT,
//     forceTLS: (import.meta.env.VITE_REVERB_SCHEME ?? 'https') === 'https',
//     enabledTransports: ['ws', 'wss'],
// });

// // sortable

// import Sortable from 'sortablejs';
// import Alpine from 'alpinejs';
// window.Alpine = Alpine;

// // === Inicializar drag & drop para buckets ===
// window.initBucketSorting = (wire) => {
//     const container = document.querySelector('[x-ref="bucketsContainer"]');
//     if (!container) return;

//     Sortable.create(container, {
//         animation: 200,
//         handle: '.bucket-handle',
//         onEnd: (evt) => {
//             const order = [...container.children].map(el => el.dataset.id);
//             wire.call('reorderBuckets', order);
//         },
//     });
// };

// // === Inicializar drag & drop para tareas ===
// window.initTaskSorting = (bucketId, wire) => {
//     const container = document.querySelector(`[data-bucket-tasks="${bucketId}"]`);
//     if (!container) return;

//     Sortable.create(container, {
//         group: 'tasks',
//         animation: 200,
//         handle: '.task-handle',
//         onEnd: (evt) => {
//             const order = [...container.children].map(el => el.dataset.id);
//             const newBucketId = evt.to.dataset.bucketId;
//             wire.call('reorderTasks', bucketId, newBucketId, order);
//         },
//     });
// };

// Configurar Echo para Reverb - Mensajería en tiempo real
window.Echo = new Echo({
    broadcaster: 'reverb',
    key: import.meta.env.VITE_REVERB_APP_KEY,
    wsHost: import.meta.env.VITE_REVERB_HOST || 'localhost',
    wsPort: import.meta.env.VITE_REVERB_PORT || 8080,
    wssPort: import.meta.env.VITE_REVERB_PORT || 8080,
    wsPath: '',
    forceTLS: (import.meta.env.VITE_REVERB_SCHEME ?? 'http') === 'https',
    enabledTransports: ['ws', 'wss'],
    disableStats: true,
    cluster: 'mt1', // Required by Pusher lib but ignored for self-hosted
    authorizer: (channel, options) => {
        return {
            authorize: (socketId, callback) => {
                axios.post('/broadcasting/auth', {
                    socket_id: socketId,
                    channel_name: channel.name
                })
                    .then(response => {
                        callback(false, response.data);
                    })
                    .catch(error => {
                        callback(true, error);
                    });
            },
        };
    },
});

// Alpine.start();

