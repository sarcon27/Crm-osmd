<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
</script>

<template>
    <AppLayout>
        <div class="p-6">
            <h2 class="text-2xl font-semibold mb-6">Dashboard</h2>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-white p-6 rounded-lg shadow">
                    <h3 class="text-lg font-medium mb-2">Total Users</h3>
                    <p class="text-3xl font-bold">1,234</p>
                </div>

                <div class="bg-white p-6 rounded-lg shadow">
                    <h3 class="text-lg font-medium mb-2">Total Products</h3>
                    <p class="text-3xl font-bold">567</p>
                </div>

                <div class="bg-white p-6 rounded-lg shadow">
                    <h3 class="text-lg font-medium mb-2">Total Orders</h3>
                    <p class="text-3xl font-bold">890</p>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
