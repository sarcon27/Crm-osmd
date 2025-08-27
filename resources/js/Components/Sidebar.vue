<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
    visible: Boolean
});

const navItems = [
    { name: 'Dashboard', icon: 'pi pi-home', route: 'dashboard' },
    { name: 'Users', icon: 'pi pi-users', route: 'users.index' },
    { name: 'Products', icon: 'pi pi-shopping-cart', route: 'products.index' },
    { name: 'Settings', icon: 'pi pi-cog', route: 'settings' },
];
</script>

<template>
    <div
        class="sidebar bg-white text-gray-700 w-64 border-r border-gray-200 transition-all duration-300 ease-in-out"
        :class="{ 'hidden': !visible, 'block': visible }"
    >
        <div class="p-4 border-b border-gray-200">
            <h2 class="text-xl font-semibold">My App</h2>
        </div>

        <nav class="mt-4">
            <ul>
                <li v-for="item in navItems" :key="item.name" class="mb-1">
                    <Link
                        :href="item.route"
                        class="flex items-center px-4 py-3 hover:bg-gray-100 transition-colors duration-200"
                        active-class="bg-blue-50 text-blue-600"
                    >
                        <i :class="item.icon" class="mr-3"></i>
                        <span>{{ item.name }}</span>
                    </Link>
                </li>
            </ul>
        </nav>
    </div>
</template>

<style scoped>

</style>
