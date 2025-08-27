<script setup>
import ServiceModal from '@/Components/Modals/ServiceModal.vue'
import {ref} from 'vue';
import EmptyPage from '@/Pages/Empty.vue'

const props = defineProps({
    title: String,
    services: Object,
    filters: Object,
});


const showModal = ref(false);
const currentService = ref(null);
const openEditModal = (service) => {
    currentService.value = service;
    showModal.value = true;
};

</script>

<template>
    <Toolbar class="mb-6">
        <template #start>
            <div class="font-semibold text-xl">Управление услугами</div>
        </template>
        <template #end>
            <Button icon="pi pi-plus" label="Добавить" @click="openEditModal(null)"/>
        </template>
    </Toolbar>

    <DataTable
        v-if="services.data?.length"
        :value="services.data"
        resizableColumns
        columnResizeMode="fit"
        responsiveLayout="scroll"
    >

        <Column header="#">
            <template #body="{ index }">
                {{ index + 1 }}
            </template>
        </Column>

        <Column field="name" header="Название" :showFilterMenu="false" style="width: 25%"/>

        <Column header="Базовый">
            <template #body="{ data }">
                <i v-if="data.is_base" class="pi pi-check text-green-500"></i>
                <i v-else class="pi pi-times text-red-500"></i>
            </template>
        </Column>

        <Column header="Автоплатеж">
            <template #body="{ data }">
                <i v-if="data.is_auto" class="pi pi-check text-green-500"></i>
                <i v-else class="pi pi-times text-red-500"></i>
            </template>
        </Column>

        <Column field="tariff_name" header="Действующий тариф" :showFilterMenu="false"/>

        <Column field="created_at" header="Добавлен"/>

        <Column style="width: 15%" header="">
            <template #body="{ data }">
                <Button icon="pi pi-pencil" type="button" class="p-button-text"
                        @click="openEditModal(data)"></Button>
            </template>
        </Column>

    </DataTable>

    <EmptyPage v-else/>

    <ServiceModal
        :show.sync="showModal"
        :service="currentService"
        @update:show="val => showModal = val"
    />
</template>
