import axios from "axios";
import { useBoardStore } from "@/stores/Board/boardStore";
import { io } from "socket.io-client";

window.axios = axios;
window.axios.defaults.headers.common["X-Requested-With"] = "XMLHttpRequest";
window.axios.defaults.withCredentials = true;

const socketServerUrl =
    import.meta.env.VITE_SOCKET_SERVER_URL || "http://localhost:6001";

const socket = io(socketServerUrl, {
    transports: ["websocket"],
});

socket.on("connect", () => {
    console.log("Connected to Socket.IO server:", socket.id);
});

socket.on("BoardUpdated", (data) => {
    console.log("Board updated event received:", data.board);
    const boardStore = useBoardStore();
    const index = boardStore.boards.findIndex((b) => b.id === data.board.id);
    if (index !== -1) {
        boardStore.boards[index] = data.board;
    } else {
        boardStore.boards.push(data.board);
    }
});

socket.on("BoardCreated", (data) => {
    console.log("Board created event received:", data.board);
    const boardStore = useBoardStore();
    const exists = boardStore.boards.some((b) => b.id === data.board.id);
    if (!exists) {
        boardStore.boards.push(data.board);
    } else {
        console.log("Board already exists in store. Ignoring creation event.");
    }
});
