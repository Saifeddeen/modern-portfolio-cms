<script setup lang="ts">
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import { Head } from '@inertiajs/vue3';
</script>

<template>

    <Head title="Dashboard" />

    <DashboardLayout>
        <div class="space-y-6">
            <div>
                <h1 class="text-2xl font-semibold text-graphite-800 dark:text-white">Dashboard</h1>
                <p class="mt-1 text-sm text-graphite-500">Welcome back. Here's what's happening with your portfolio.</p>
            </div>

            <!-- Minimal Stats Grid -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div
                    class="p-6 bg-white dark:bg-graphite-950 rounded-xl shadow-soft border border-graphite-200 dark:border-graphite-800">
                    <p class="text-sm font-medium text-graphite-500">Total Projects</p>
                    <p class="text-3xl font-semibold text-graphite-800 dark:text-white mt-2">0</p>
                </div>
                <div
                    class="p-6 bg-white dark:bg-graphite-950 rounded-xl shadow-soft border border-graphite-200 dark:border-graphite-800">
                    <p class="text-sm font-medium text-graphite-500">Total Messages</p>
                    <p class="text-3xl font-semibold text-graphite-800 dark:text-white mt-2">0</p>
                </div>
                <div
                    class="p-6 bg-white dark:bg-graphite-950 rounded-xl shadow-soft border border-graphite-200 dark:border-graphite-800">
                    <p class="text-sm font-medium text-graphite-500">Profile Completion</p>
                    <p class="text-3xl font-semibold text-graphite-800 dark:text-white mt-2">100%</p>
                </div>
            </div>
        </div>
    </DashboardLayout>
</template>