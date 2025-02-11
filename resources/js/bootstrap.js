import axios from "axios";
import { useBoardStore } from "@/stores/Board/boardStore";
import { useColumnStore } from "@/stores/Column/columnStore";
import { useTaskStore } from "@/stores/Task/taskStore";

window.axios = axios;
window.axios.defaults.headers.common["X-Requested-With"] = "XMLHttpRequest";
window.axios.defaults.withCredentials = true;


