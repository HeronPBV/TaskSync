<script setup>
import { Head, Link } from "@inertiajs/vue3";

// Props
defineProps({
    canLogin: Boolean,
    canRegister: Boolean,
});
</script>

<template>
    <Head title="Welcome to TaskSync" />

    <div
        class="relative min-h-screen bg-gray-100 dark:bg-gray-900 flex flex-col items-center justify-center text-center p-6"
    >
        <!-- Background Banner -->
        <div class="absolute inset-0 z-0">
            <img
                src="/public/images/banner.jpg"
                alt="TaskSync Banner"
                class="w-full h-full object-cover brightness-75"
            />
        </div>

        <!-- Content Wrapper -->
        <div
            class="relative z-10 max-w-3xl bg-white/90 dark:bg-gray-800 rounded-xl shadow-lg p-8"
        >
            <!-- Logo and Title -->
            <div class="mb-6">
                <h1 class="text-4xl font-bold text-gray-900 dark:text-white">
                    TaskSync
                </h1>
                <p class="mt-2 text-gray-600 dark:text-gray-300">
                    A powerful and intuitive task management tool to enhance
                    your productivity.
                </p>
            </div>

            <!-- Call to Action -->
            <div class="mt-6">
                <div v-if="canLogin" class="flex justify-center space-x-4">
                    <Link
                        :href="route('login')"
                        class="px-6 py-3 bg-blue-600 text-white font-semibold rounded-lg shadow-md hover:bg-blue-700 transition"
                    >
                        Log in
                    </Link>

                    <Link
                        v-if="canRegister"
                        :href="route('register')"
                        class="px-6 py-3 bg-gray-300 text-gray-900 font-semibold rounded-lg shadow-md hover:bg-gray-400 transition"
                    >
                        Register
                    </Link>
                </div>
            </div>
        </div>
    </div>
</template>
