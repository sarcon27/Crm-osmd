<script setup>
import { ref } from 'vue';
import Avatar from 'primevue/avatar';
import Menu from 'primevue/menu';

defineEmits(['toggle-sidebar']);

const menu = ref();
const items = ref([
    {
        label: 'Profile',
        icon: 'pi pi-user',
        command: () => {
            window.location.href = route('profile.edit');
        }
    },
    {
        label: 'Logout',
        icon: 'pi pi-sign-out',
        command: () => {
            router.post(route('logout'));
        }
    }
]);

const toggleMenu = (event) => {
    menu.value.toggle(event);
};
</script>

<template>
    <header class="bg-white border-b border-gray-200">
        <div class="flex items-center justify-between px-4 py-3">
            <div class="flex items-center">
                <button
                    @click="$emit('toggle-sidebar')"
                    class="p-2 rounded-md hover:bg-gray-100 mr-2"
                >
                    <i class="pi pi-bars"></i>
                </button>
                <h1 class="text-xl font-semibold">
                    <slot name="title" />
                </h1>
            </div>

            <div class="flex items-center space-x-4">
                <button class="p-2 rounded-full hover:bg-gray-100">
                    <i class="pi pi-bell"></i>
                </button>

                <div class="relative">
                    <button @click="toggleMenu" class="flex items-center space-x-2 focus:outline-none">
                        <Avatar icon="pi pi-user" shape="circle" />
                        <span class="hidden md:inline">User Name</span>
                        <i class="pi pi-chevron-down"></i>
                    </button>

                    <Menu ref="menu" :model="items" :popup="true" />
                </div>
            </div>
        </div>
    </header>
</template>
