/**
 * We'll load the axios HTTP library which allows us to easily issue requests
 * to our Laravel back-end. This library automatically handles sending the
 * CSRF token as a header based on the value of the "XSRF" token cookie.
 */

import axios from "axios";
import { useBoardStore } from "@/stores/Board/boardStore";
import { io } from "socket.io-client";

window.axios = axios;
window.axios.defaults.headers.common["X-Requested-With"] = "XMLHttpRequest";

const socketServerUrl =
    import.meta.env.VITE_SOCKET_SERVER_URL || "http://localhost:6001";

const socket = io(socketServerUrl, { transports: ["websocket"] });

socket.on("connect", () => {
    console.log("Connected to Socket.IO server:", socket.id);
});

socket.on("boardUpdated", (data) => {
    console.log("Board updated event received:", data.board);

    const boardStore = useBoardStore();
    const existingBoard = boardStore.boards.find((b) => b.id === data.board.id);

    if (existingBoard && existingBoard.name !== data.board.name) {
        boardStore.updateBoard(data.board.id, data.board);
    }
});

/**
 * Echo exposes an expressive API for subscribing to channels and listening
 * for events that are broadcast by Laravel. Echo and event broadcasting
 * allows your team to easily build robust real-time web applications.
 */

// import Echo from 'laravel-echo';

// import Pusher from 'pusher-js';
// window.Pusher = Pusher;

// window.Echo = new Echo({
//     broadcaster: 'pusher',
//     key: import.meta.env.VITE_PUSHER_APP_KEY,
//     cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER ?? 'mt1',
//     wsHost: import.meta.env.VITE_PUSHER_HOST ? import.meta.env.VITE_PUSHER_HOST : `ws-${import.meta.env.VITE_PUSHER_APP_CLUSTER}.pusher.com`,
//     wsPort: import.meta.env.VITE_PUSHER_PORT ?? 80,
//     wssPort: import.meta.env.VITE_PUSHER_PORT ?? 443,
//     forceTLS: (import.meta.env.VITE_PUSHER_SCHEME ?? 'https') === 'https',
//     enabledTransports: ['ws', 'wss'],
// });
