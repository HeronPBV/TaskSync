import axios from "axios";
import { socketService } from "@/services/socketService";

window.axios = axios;
window.axios.defaults.headers.common["X-Requested-With"] = "XMLHttpRequest";
window.axios.defaults.withCredentials = true;

axios.interceptors.request.use((config) => {
    const socketId = socketService.getClientId();
    if (socketId) {
        config.headers["X-Socket-ID"] = socketId;
    }
    return config;
});
