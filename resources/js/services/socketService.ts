import { io, Socket } from "socket.io-client";

class SocketService {
    private readonly socket: Socket;
    private clientId: string | null = null;

    constructor() {
        const socketServerUrl =
            import.meta.env.VITE_SOCKET_SERVER_URL || "http://localhost:6001";

        this.socket = io(socketServerUrl, {
            transports: ["websocket"],
        });

        this.socket.on("connect", () => {
            this.clientId = this.socket.id ?? null;
            console.log("Connected to Socket.IO server:", this.clientId);
        });
    }

    getClientId(): string | null {
        return this.clientId;
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
        this.socket.emit(event, { ...data, clientId: this.clientId });
    }
}

// Export a singleton instance
export const socketService = new SocketService();
