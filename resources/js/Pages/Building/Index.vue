<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import {Head, router} from '@inertiajs/vue3';
import BuildingModal from '@/Components/Modals/BuildingModal.vue'
import { ref } from 'vue'

const props = defineProps({
    title: String,
    buildings: Object,
    basicServices: Object,
    filters: Object

});

const onPage = (event) => {
    const targetPage = event.page + 1;
    const link = props.buildings.meta.links.find(l => l.label == targetPage);

    if (link?.url) {
        router.visit(link.url, {
            preserveScroll: true,
            preserveState: true
        });
    }
};

const onSort = (event) => {
    const sortField = event.sortField;
    const sortOrder = event.sortOrder === 1 ? sortField : `-${sortField}`;
    router.get(route('dashboard.buildings.index'), {
        sort: sortOrder,
        page: props.buildings.meta.current_page,
        per_page: props.buildings.meta.per_page
    }, {
        preserveScroll: true,
        preserveState: true
    });
};

const onServerFilter = (field, value) => {
    router.get(route('dashboard.buildings.index'), {
        ...props.filters,
        [`filter[${field}]`]: value,
        page: 1
    }, {
        preserveScroll: true,
        preserveState: true
    });
};

const showModal = ref(false);

const currentBuilding = ref(null);

const openEditModal = (building) => {
    currentBuilding.value = building;
    showModal.value = true;
};

</script>

<template>
    <app-layout>
        <Head :title="title"/>

        <div class="card">
            <Toolbar class="mb-6" >
                <template #start>
                    <div class="font-semibold text-xl mb-4">Управление домами</div>
                </template>
                <template #end>
                    <Button icon="pi pi-plus" label="Добавить"  @click="openEditModal(null)"/>
                </template>
            </Toolbar>

            <DataTable
                :value="buildings.data"
                :rows="buildings.meta.per_page"
                :totalRecords="buildings.meta.total"
                :paginator="true"
                :lazy="true"
                filterDisplay="row"
                resizableColumns
                columnResizeMode="fit"
                responsiveLayout="scroll"
                @page="onPage"
                @sort="onSort"

            >
                <Column
                    header="#"
                    bodyStyle="text-align:center"
                >
                    <template #body="{ index }">
                        {{ (buildings.meta.current_page - 1) * buildings.meta.per_page + index + 1 }}
                    </template>
                </Column>

                <Column field="name" header="Название" :sortable="true" style="width: 35%" :showFilterMenu="false">
                    <template #body="{ data }">
                        {{ data.name }}
                    </template>
                    <template #filter>
                        <InputText
                            :value="props.filters?.filter?.name || ''"
                            @input="e => onServerFilter('name', e.target.value)"
                            placeholder="Поиск по имени"
                            class="p-inputtext p-component"
                        />
                        <Button  v-if="props.filters?.filter?.name" icon="pi pi-times" class="p-button-text"
                                @click="onServerFilter('name', '')"
                        />
                    </template>
                </Column>

                <Column field="address" header="Адрес" :sortable="true" style="width: 35%" :showFilterMenu="false">
                    <template #body="{ data }">
                        {{ data.address }}
                    </template>
                    <template #filter>
                        <InputText
                            :value="props.filters?.filter?.address || ''"
                            @input="e => onServerFilter('address', e.target.value)"
                            placeholder="Поиск по адресу"
                            class="p-inputtext p-component"
                        />
                        <Button  v-if="props.filters?.filter?.address" icon="pi pi-times" class="p-button-text"
                                @click="onServerFilter('address', '')"
                        />
                    </template>
                </Column>

                <Column field="description" header="Описание" style="width: 35%">
                    <template #body="{ data }">
                        {{ data.description?.length > 40 ? data.description.substring(0, 40) + '...' : data.description }}
                    </template>
                </Column>

                <Column field="countSections" header="Секций" bodyStyle="text-align:center"/>
                <Column field="created_at" header="Добавлен" bodyStyle="text-align:center"/>

                <Column style="width: 15%" header="">
                    <template #body="{ data }">
                        <Button icon="pi pi-pencil" type="button" class="p-button-text" @click="openEditModal(data)"></Button>
                    </template>
                </Column>

            </DataTable>
        </div>

        <BuildingModal
            :show.sync="showModal"
            :building="currentBuilding"
            :basicServices="basicServices"
            @update:show="val => showModal = val"
        />
    </app-layout>
</template>
