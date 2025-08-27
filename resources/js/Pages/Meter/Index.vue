<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import {Head, router} from '@inertiajs/vue3';
import BuildingModal from '@/Components/Modals/BuildingModal.vue'
import { ref } from 'vue'

const props = defineProps({
    title: String,
    meters: Object,
    filters: Object

});

const onPage = (event) => {
    const targetPage = event.page + 1;
    const link = props.meters.meta.links.find(l => l.label == targetPage);

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
    router.get(route('dashboard.meters.index'), {
        sort: sortOrder,
        page: props.meters.meta.current_page,
        per_page: props.meters.meta.per_page
    }, {
        preserveScroll: true,
        preserveState: true
    });
};

const onServerFilter = (field, value) => {
    router.get(route('dashboard.meters.index'), {
        ...props.filters,
        [`filter[${field}]`]: value,
        page: 1
    }, {
        preserveScroll: true,
        preserveState: true
    });
};

const showModal = ref(false);

const currentMeter = ref(null);

const periodFilter = ref(null);

const formatPeriod = (date) => {
    if (!date) return "";
    const year = date.getFullYear();
    const month = String(date.getMonth() + 1).padStart(2, "0");
    return `${year}-${month}`;
};

const openEditModal = (building) => {
    currentMeter.value = building;
    showModal.value = true;
};

</script>

<template>
    <app-layout>
        <Head :title="title"/>

        <div class="card">
            <Toolbar class="mb-6" >
                <template #start>
                    <div class="font-semibold text-xl">Показания счетчиков</div>
                </template>
<!--                <template #end>-->
<!--                    <Button icon="pi pi-plus" label="Добавить"  @click="openEditModal(null)"/>-->
<!--                </template>-->
            </Toolbar>

            <DataTable
                :value="meters.data"
                :rows="meters.meta.per_page"
                :totalRecords="meters.meta.total"
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
                        {{ (meters.meta.current_page - 1) * meters.meta.per_page + index + 1 }}
                    </template>
                </Column>

                <Column field="status" header="Услуга" style="width: 10%" :showFilterMenu="false">
                    <template #body="{ data }">
                        {{ data.service.name }}
                    </template>
                    <template #filter>
                        <Select
                            :options="meters.meta.serviceOptions"
                            optionLabel="name"
                            optionValue="id"
                            v-model="filters.service_id"
                            placeholder="Выберите"
                            @change="onServerFilter('service_id', filters.service_id)"
                            fluid
                            :showClear="true"
                        />

                    </template>
                </Column>

                <Column field="address" header="Адрес" :sortable="true" style="width: 35%" :showFilterMenu="false">
                    <template #body="{ data }">
                        {{ data.apartment?.building?.address }}
                    </template>
                    <template #filter>
                        <InputText
                            :value="props.filters?.filter?.address || ''"
                            @input="e => onServerFilter('address', e.target.value)"
                            placeholder="Поиск по адресу"
                            class="p-inputtext p-component"
                            fluid
                        />
                        <Button  v-if="props.filters?.filter?.address" icon="pi pi-times" class="p-button-text"
                                @click="onServerFilter('address', '')"
                        />
                    </template>
                </Column>

                <Column field="number" header="Квартира" :sortable="true"  :showFilterMenu="false">
                    <template #body="{ data }">
                        {{ data.apartment?.number }}
                    </template>
                    <template #filter>
                        <InputText
                            :value="props.filters?.filter?.number || ''"
                            @input="e => onServerFilter('number', e.target.value)"
                            placeholder="Поиск по квартире"
                            class="p-inputtext p-component"
                        />
                        <Button  v-if="props.filters?.filter?.number" icon="pi pi-times" class="p-button-text"
                                 @click="onServerFilter('number', '')"
                        />
                    </template>
                </Column>

                <Column field="period" header="Период" bodyStyle="text-align:center" :showFilterMenu="false">
                    <template #body="{ data }">
                        {{ data.period }}
                    </template>

                    <template #filter>
                        <DatePicker
                            v-model="periodFilter"
                            view="month"
                            dateFormat="yy-mm"
                            placeholder="Выберите месяц"
                            showIcon
                            @update:modelValue="val => onServerFilter('period', formatPeriod(val))"
                        />
                        <Button
                            v-if="props.filters?.filter?.period"
                            icon="pi pi-times"
                            class="p-button-text ml-2"
                            @click="() => { periodFilter = null; onServerFilter('period', '') }"
                        />
                    </template>
                </Column>

                <Column field="new_value" header="Новое значение" bodyStyle="text-align:center"/>
                <Column field="old_value" header="Старое значение" bodyStyle="text-align:center"/>

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
