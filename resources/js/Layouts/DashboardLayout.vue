<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { Link } from '@inertiajs/vue3';
import { Menu, Moon, Sun, LayoutDashboard, User, Folder, Briefcase, Settings, LogOut } from '@lucide/vue';
// Import the global components
import Toast from 'primevue/toast';
import ConfirmDialog from 'primevue/confirmdialog';

const sidebarOpen = ref(false);
const isDark = ref(false);

const navigation = [
    { name: 'Dashboard', route: 'dashboard', icon: LayoutDashboard },
    { name: 'Profile', route: 'profile.edit', icon: User },
    { name: 'Projects', route: 'dashboard', icon: Folder },
    { name: 'Experience', route: 'dashboard', icon: Briefcase },
    { name: 'Settings', route: 'dashboard', icon: Settings },
];

onMounted(() => {
    isDark.value = document.documentElement.classList.contains('app-dark');
});

const toggleDarkMode = () => {
    const html = document.documentElement;
    isDark.value = !isDark.value;

    if (isDark.value) {
        html.classList.add('dark');
        html.classList.add('app-dark');
        localStorage.theme = 'dark';
    } else {
        html.classList.remove('dark');
        html.classList.remove('app-dark');
        localStorage.theme = 'light';
    }
};
</script>

<template>
    <div class="min-h-screen bg-graphite-50 dark:bg-graphite-900 transition-colors duration-300">
        <!-- Global PrimeVue Overlays -->
        <Toast position="top-right" />
        <ConfirmDialog />

        <!-- Sidebar -->
        <aside
            class="fixed top-0 left-0 z-40 h-screen w-64 transition-transform duration-300 -translate-x-full lg:translate-x-0 bg-white dark:bg-graphite-950 border-r border-graphite-200 dark:border-graphite-800"
            :class="{ 'translate-x-0': sidebarOpen }">
            <div class="flex items-center h-16 px-6 border-b border-graphite-200 dark:border-graphite-800">
                <span class="text-lg font-semibold text-graphite-800 dark:text-white">Portfolio CMS</span>
            </div>
            <nav class="p-4 space-y-1">
                <Link v-for="item in navigation" :key="item.name" :href="route(item.route)"
                    class="flex items-center px-4 py-2.5 text-sm font-medium text-graphite-500 rounded-lg transition-all duration-200 hover:bg-graphite-100 hover:text-graphite-800 dark:hover:bg-graphite-800 dark:hover:text-white"
                    :class="{ 'bg-iris-50 text-iris-600 dark:bg-iris-900/30 dark:text-iris-400': route().current(item.route) }">
                    <component :is="item.icon" class="w-5 h-5 mr-3" />
                    {{ item.name }}
                </Link>
            </nav>
            <div class="absolute bottom-0 w-full p-4 border-t border-graphite-200 dark:border-graphite-800">
                <Link :href="route('logout')" method="post" as="button"
                    class="flex items-center w-full px-4 py-2.5 text-sm font-medium text-graphite-500 rounded-lg hover:bg-red-50 hover:text-red-600 dark:hover:bg-red-900/20 dark:hover:text-red-400 transition-colors">
                    <LogOut class="w-5 h-5 mr-3" />
                    Logout
                </Link>
            </div>
        </aside>

        <!-- Main Content -->
        <div class="lg:ml-64">
            <!-- Topbar -->
            <header
                class="flex items-center justify-between h-16 px-6 bg-white/80 dark:bg-graphite-950/80 border-b border-graphite-200 dark:border-graphite-800 backdrop-blur-sm sticky top-0 z-30">
                <button @click="sidebarOpen = !sidebarOpen" class="text-graphite-500 lg:hidden">
                    <Menu class="w-6 h-6" />
                </button>

                <div class="flex items-center ml-auto space-x-4">
                    <button @click="toggleDarkMode"
                        class="p-2 rounded-lg text-graphite-400 hover:bg-graphite-100 dark:hover:bg-graphite-800 transition-colors">
                        <Moon v-if="!isDark" class="w-5 h-5" />
                        <Sun v-else class="w-5 h-5" />
                    </button>
                    <div class="flex items-center">
                        <div
                            class="w-8 h-8 rounded-full bg-iris-500 flex items-center justify-center text-white font-semibold text-sm">
                            {{ $page.props.auth.user.name.charAt(0) }}
                        </div>
                        <span
                            class="ml-3 text-sm font-medium text-graphite-700 dark:text-graphite-300 hidden sm:block">{{
                                $page.props.auth.user.name }}</span>
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <main class="p-8 max-w-7xl mx-auto">
                <slot />
            </main>
        </div>
    </div>
</template>