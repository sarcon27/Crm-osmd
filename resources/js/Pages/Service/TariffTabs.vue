<script setup>
import TariffModal from '@/Components/Modals/TariffModal.vue'
import {useServerTable} from '@/composables/useServerTable';
import EmptyPage from '@/Pages/Empty.vue'
import {ref} from 'vue';

const props = defineProps({
    title: String,
    tariffs: Object,
    measures: Object,
    services: Object,
    filters: Object,

});

const showModal = ref(false);
const currentTariff = ref(null);
const filters = ref(props.filters?.filter || {});
const meta = ref(props.tariffs.meta || {})

const getStatusSeverity = (status) => {
    switch (status) {
        case 'active':
            return 'success';
        case 'archive':
            return 'info';
        case 'inactive':
            return 'danger';
        default:
            return 'secondary';
    }
};

const openEditModal = (tariff) => {
    currentTariff.value = tariff;
    showModal.value = true;
};

const {onPage, onSort, onServerFilter} = useServerTable('dashboard.services.index', filters, meta);

</script>

<template>
    <Toolbar class="mb-6">
        <template #start>
            <div class="font-semibold text-xl">Управление тарифами</div>
        </template>
        <template #end>
            <Button icon="pi pi-plus" label="Добавить" @click="openEditModal(null)"/>
        </template>
    </Toolbar>

    <DataTable
        v-if="tariffs.data?.length"
        :value="tariffs.data"
        resizableColumns
        columnResizeMode="fit"
        responsiveLayout="scroll"
        filterDisplay="row"
        stripedRows

        :rows="tariffs.meta.per_page"
        :totalRecords="tariffs.meta.total"
        :paginator="tariffs?.meta?.total > tariffs?.meta?.per_page"
        :lazy="true"
        @page="onPage"
        @sort="onSort"
    >

        <Column
            header="#"
            bodyStyle="text-align:center"
        >
            <template #body="{ index }">
                {{ (tariffs.meta.current_page - 1) * tariffs.meta.per_page + index + 1 }}
            </template>
        </Column>

        <Column field="service.name" header="Услуга, грн"/>

        <Column field="status" header="Статус" :showFilterMenu="false" sortable="true">
            <template #body="{ data }">
                <Tag
                    :value="data.status.label"
                    :severity="getStatusSeverity(data.status.value)"
                />
            </template>
            <template #filter>
                <Select
                    :options="tariffs.meta.statusOptions"
                    optionLabel="label"
                    optionValue="value"
                    v-model="filters.status"
                    placeholder="Выберите статус"
                    @change="onServerFilter('status', filters.status)"
                    fluid
                    :showClear="true"
                />
            </template>
        </Column>

        <Column field="type" header="Тип" :showFilterMenu="false">
            <template #body="{ data }">
                <Tag severity="secondary" :value="data.type.label"/>
            </template>
            <template #filter>
                <Select
                    :options="tariffs.meta.typeOptions"
                    optionLabel="label"
                    optionValue="value"
                    v-model="filters.type"
                    placeholder="Выберите тип"
                    @change="onServerFilter('type', filters.type)"
                    fluid
                    :showClear="true"
                />
            </template>
        </Column>


        <Column field="price" header="Цена за единицу, грн"/>

        <Column field="measure.name" header="Ед. изм"/>

        <Column field="start_date" header="Начало" sortable="true"/>

        <Column field="end_date" header="Конец" sortable="true"/>

        <Column field="created_at" header="Добавлен" sortable="true"/>

        <Column style="width: 15%" header="">
            <template #body="{ data }">
                <Button icon="pi pi-pencil" type="button" class="p-button-text"
                        @click="openEditModal(data)"></Button>
            </template>
        </Column>

    </DataTable>

    <EmptyPage v-else/>


    <TariffModal
        :show.sync="showModal"
        :tariff="currentTariff"
        :measures="measures"
        :services="services"
        :statusOptions="tariffs.meta.statusOptions"
        :typeOptions="tariffs.meta.typeOptions"
        @update:show="val => showModal = val"
    />

</template>
