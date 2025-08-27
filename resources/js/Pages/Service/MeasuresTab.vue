<script setup>
import MeasureModal from '@/Components/Modals/MeasureModal.vue'
import {ref} from 'vue';
import EmptyPage from '@/Pages/Empty.vue'

const props = defineProps({
    title: String,
    measures: Object,
    filters: Object,

});

const showModal = ref(false);

const currentMeasure = ref(null);

const openEditModal = (measure) => {
    currentMeasure.value = measure;
    showModal.value = true;
};

</script>

<template>
    <Toolbar class="mb-6">
        <template #start>
            <div class="font-semibold text-xl">Управление единицами измерений</div>
        </template>
        <template #end>
            <Button icon="pi pi-plus" label="Добавить" @click="openEditModal(null)"/>
        </template>
    </Toolbar>

    <DataTable
        v-if="measures.data?.length"
        :value="measures.data"
        resizableColumns
        columnResizeMode="fit"
        responsiveLayout="scroll"
    >

        <Column field="name" header="Название" style="width: 75%"/>

        <Column field="created_at" header="Добавлен" style="width: 10%"/>

        <Column style="width: 5%" header="">
            <template #body="{ data }">
                <Button icon="pi pi-pencil" type="button" class="p-button-text"
                        @click="openEditModal(data)"></Button>
            </template>
        </Column>

    </DataTable>

    <EmptyPage v-else/>

    <MeasureModal
        :show.sync="showModal"
        :measure="currentMeasure"
        :measures="measures"
        @update:show="val => showModal = val"
    />

</template>
