<script setup>
import {usePage} from '@inertiajs/vue3'

import AppMenuItem from './AppMenuItem.vue';

const menu = usePage().props.menu

</script>

<template>
    <ul class="layout-menu">
        <template v-for="(item, i) in menu" :key="item">
            <app-menu-item v-if="!item.separator" :item="item" :index="i"></app-menu-item>
            <li v-if="item.separator" class="menu-separator"></li>
        </template>
    </ul>
</template>

<style lang="scss" scoped></style>
