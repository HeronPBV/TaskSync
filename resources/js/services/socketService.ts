import { io, Socket } from "socket.io-client";

class SocketService {
    private socket: Socket;

    constructor() {
        const socketServerUrl =
            import.meta.env.VITE_SOCKET_SERVER_URL || "http://localhost:6001";

        this.socket = io(socketServerUrl, {
            transports: ["websocket"],
        });

        this.socket.on("connect", () => {
            console.log("Connected to Socket.IO server:", this.socket.id);
        });
    }

    /**
     * Register an event listener for a specific event.
     * @param event - The event name (e.g., "BoardUpdated").
     * @param callback - The function to execute when the event is received.
     */
    on(event: string, callback: (data: any) => void) {
        this.socket.on(event, callback);
    }

    /**
     * Remove an event listener.
     * @param event - The event name to remove.
     */
    off(event: string) {
        this.socket.off(event);
    }

    /**
     * Emit an event manually (if needed).
     * @param event - The event name.
     * @param data - The payload to send.
     */
    emit(event: string, data: any) {
        this.socket.emit(event, data);
    }
}

// Export a singleton instance
export const socketService = new SocketService();
