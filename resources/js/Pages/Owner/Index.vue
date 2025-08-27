<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import {Head, router} from '@inertiajs/vue3';
import OwnerModal from '@/Components/Modals/OwnerModal.vue'
import {ref, reactive, computed} from 'vue';

const props = defineProps({
    title: String,
    owners: Object,
    filters: Object,

});

const filters = reactive({});

const onServerFilter = (field, value) => {
    // Создаём новый объект фильтров в формате filter[field]=value
    const filterParams = {
        ...filters,
        [field]: value
    };

    // Преобразуем в filter[field]=value
    const query = Object.fromEntries(
        Object.entries(filterParams)
            .filter(([_, v]) => v !== null && v !== '')
            .map(([k, v]) => [`filter[${k}]`, v])
    );

    router.get(route('dashboard.owners.index'), query, {
        preserveScroll: true,
        preserveState: true
    });
};

const clearFilter = (fieldName) => {
    filters[fieldName] = '';
    onServerFilter(fieldName, '');
}

const onPage = (event) => {
    const targetPage = event.page + 1;
    const link = props.owners.meta.links.find(l => l.label == targetPage);

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
    router.get(route('dashboard.owners.index'), {
        sort: sortOrder,
        page: props.owners.meta.current_page,
        per_page: props.owners.meta.per_page
    }, {
        preserveScroll: true,
        preserveState: true
    });
};


const showModal = ref(false);

const currentOwner = ref(null);

const openEditModal = (owner) => {
    currentOwner.value = owner;
    showModal.value = true;
};

</script>

<template>
    <app-layout>
        <Head :title="title"/>

        <div class="card">
            <Toolbar class="mb-6">
                <template #start>
                    <div class="font-semibold text-xl">Управление жителями</div>
                </template>
                <template #end>
                    <Button icon="pi pi-plus" label="Добавить" @click="openEditModal(null)"/>
                </template>
            </Toolbar>

            <DataTable
                :value="owners.data"
                :rows="owners.meta.per_page"
                :totalRecords="owners.meta.total"
                :paginator="owners.meta.total > owners.meta.per_page"
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
                        {{ (owners.meta.current_page - 1) * owners.meta.per_page + index + 1 }}
                    </template>
                </Column>

                <Column field="name" header="ФИО" :showFilterMenu="false" style="width: 55%" :sortable="true">
                    <template #filter>
                        <InputText
                            v-model="filters.name"
                            placeholder="ФИО"
                            @input="onServerFilter('name', filters.name)"
                            class="p-inputtext p-component"
                            :showClear="true"
                        />
                        <Button
                            v-if="filters.name"
                            icon="pi pi-times"
                            class="p-button-text"
                            @click="clearFilter('name')"
                        />

                    </template>
                </Column>

                <Column field="phone" header="Телефон" :showFilterMenu="false" :sortable="true">
                    <template #filter>
                        <InputText
                            v-model="filters.phone"
                            placeholder="Телефон"
                            @input="onServerFilter('phone', filters.phone)"
                            class="p-inputtext p-component"
                            :showClear="true"
                        />
                        <Button
                            v-if="filters.phone"
                            icon="pi pi-times"
                            class="p-button-text"
                            @click="clearFilter('phone')"
                        />
                    </template>
                </Column>

                <Column field="email" header="Email" :showFilterMenu="false" :sortable="true">
                    <template #filter>
                        <InputText
                            v-model="filters.email"
                            placeholder="Email"
                            @input="onServerFilter('email', filters.email)"
                            class="p-inputtext p-component"
                            :showClear="true"
                        />
                        <Button
                            v-if="filters.email"
                            icon="pi pi-times"
                            class="p-button-text"
                            @click="clearFilter('email')"
                        />
                    </template>
                </Column>

                <Column field="apartments_count" header="Квартир" bodyStyle="text-align:center"/>

                <Column field="created_at" header="Добавлен" bodyStyle="text-align:center"/>

                <Column style="width: 15%" header="">
                    <template #body="{ data }">
                        <Button icon="pi pi-pencil" type="button" class="p-button-text"
                                @click="openEditModal(data)"></Button>
                    </template>
                </Column>

            </DataTable>
        </div>

        <OwnerModal
            :show.sync="showModal"
            :owner="currentOwner"
            @update:show="val => showModal = val"
        />
    </app-layout>
</template>
