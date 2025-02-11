import { defineStore } from "pinia";
import type { User } from "@/interfaces/User/User";
import axios from "axios";

export const useUserStore = defineStore("userStore", {
    state: () => ({
        user: null as User | null,
        loading: false,
        error: null as string | null,
    }),
    actions: {
        /**
         * Fetches the authenticated user from the backend.
         */
        async fetchUser() {
            this.loading = true;
            try {
                const response = await axios.get("/user");
                this.user = response.data ?? { id: -1 };
            } catch (error) {
                console.error("Error fetching user:", error);
            } finally {
                this.loading = false;
            }
        },

        /**
         * Logs out the user and resets the state.
         */
        async logout() {
            try {
                await axios.post("/logout");
                this.user = null;
            } catch (error) {
                console.error("Error logging out:", error);
            }
        },
    },
});
