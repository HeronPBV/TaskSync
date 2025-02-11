<template>
    <div
        class="relative flex flex-col h-full border rounded-lg p-6 shadow hover:shadow-xl transition duration-200"
        @dblclick="toggleEdit"
    >
        <button
            v-if="canInteract"
            @click="handleDelete"
            class="absolute -top-1 -left-1 bg-red-300 text-red-600 p-2 rounded-full shadow w-5 h-5 hover:bg-red-400 flex justify-center items-center text-xl"
            title="Delete Board"
        >
            &times;
        </button>

        <div v-if="isEditing && canInteract">
            <BoardEditForm
                :board="board"
                @board-updated="handleBoardUpdated"
                @cancel-edit="toggleEdit"
            />
        </div>
        <div v-else>
            <div>
                <h2 class="text-xl font-semibold mb-2">{{ board.name }}</h2>
                <p class="text-gray-600" v-if="board.description">
                    {{ board.description }}
                </p>
            </div>
            <div class="mt-auto pt-4">
                <Link
                    :href="route('boards.show', board.id)"
                    class="inline-block bg-blue-300 hover:bg-blue-400 text-blue-600 font-bold py-2 px-4 rounded transition duration-200 cursor-pointer shadow"
                    v-if="canInteract"
                >
                    Open Board
                </Link>
            </div>
        </div>
    </div>
</template>

<script lang="ts" setup>
import { defineProps, ref, computed } from "vue";
import { route } from "ziggy-js";
import { Link } from "@inertiajs/vue3";
import { useBoardStore } from "@/stores/Board/boardStore";
import { useUserStore } from "@/stores/User/userStore";
import type { Board } from "@/interfaces/Board/Board";
import BoardEditForm from "@/Components/Board/BoardEditForm.vue";

const props = defineProps<{ board: Board }>();

const boardStore = useBoardStore();
const userStore = useUserStore();

const isEditing = ref(false);

const toggleEdit = () => {
    if (canInteract) {
        isEditing.value = !isEditing.value;
    }
};

const handleBoardUpdated = (updatedBoard: Board) => {
    isEditing.value = false;
};

const canInteract = computed(() => {
    return (
        userStore.user &&
        props.board.user_id &&
        userStore.user.id === props.board.user_id
    );
});

const handleDelete = async () => {
    if (
        confirm(
            "Are you sure you want to delete this board? All data associated with this board will be permanently deleted."
        )
    ) {
        try {
            await boardStore.deleteBoard(props.board.id);
        } catch (error) {
            console.error("Error deleting board:", error);
        }
    }
};
</script>
