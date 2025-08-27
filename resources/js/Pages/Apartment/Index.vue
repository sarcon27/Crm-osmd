<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import {Head, router} from '@inertiajs/vue3';
import ApartmentModal from '@/Components/Modals/ApartmentModal.vue'
import {ref, reactive, computed} from 'vue';
import InvoiceModal from "@/Components/Modals/InvoiceModal.vue";
import MeterModal from "@/Components/Modals/MeterModal.vue";

const props = defineProps({
    title: String,
    apartments: Object,
    filters: Object,
    buildings: Object,
    extraServices: Object
});

const filters = reactive({
    building_id: props.filters.building_id || null,
    section_id: props.filters.section_id || null

});

const availableSections = computed(() => {
    const building = props.buildings.data.find(b => b.id === filters.building_id);
    return building ? building.sections : [];
});

const onServerFilter = (field, value) => {

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

    router.get(route('dashboard.apartments.index'), query, {
        preserveScroll: true,
        preserveState: true
    });
};

const onBuildingFilterChange = (buildingId) => {
    filters.building_id = buildingId;
    filters.section_id = null;
    onServerFilter('building_id', buildingId);
    onServerFilter('section_id', null);
};

const onSectionFilterChange = (sectionId) => {
    filters.section_id = sectionId;
    onServerFilter('section_id', sectionId);
};
const onPage = (event) => {
    const targetPage = event.page + 1;
    const link = props.apartments.meta.links.find(l => l.label == targetPage);

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
    router.get(route('dashboard.apartments.index'), {
        sort: sortOrder,
        page: props.apartments.meta.current_page,
        per_page: props.apartments.meta.per_page
    }, {
        preserveScroll: true,
        preserveState: true
    });
};


const showModal = ref(false);
const showInvoiceModal = ref(false);
const showMeterModal = ref(false);

const currentApartment = ref(null);

const openEditModal = (apartment) => {
    currentApartment.value = apartment;
    showModal.value = true;
};

const openInvoiceModal = (apartment) => {
    currentApartment.value = apartment;
    showInvoiceModal.value = true;
};

const openMeterModal = (apartment) => {
    currentApartment.value = apartment;
    showMeterModal.value = true;
};

</script>

<template>
    <app-layout>
        <Head :title="title"/>

        <div class="card">
            <Toolbar class="mb-6">
                <template #start>
                    <div class="font-semibold text-xl">Управление квартирами</div>
                </template>
                <template #end>
                    <Button icon="pi pi-plus" label="Добавить" @click="openEditModal(null)"/>
                </template>
            </Toolbar>

            <DataTable
                :value="apartments.data"
                :rows="apartments.meta.per_page"
                :totalRecords="apartments.meta.total"
                :paginator="apartments.meta.total > apartments.meta.per_page"
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
                        {{ (apartments.meta.current_page - 1) * apartments.meta.per_page + index + 1 }}
                    </template>
                </Column>

                <Column field="financeAccount.number" header="ЛС" :showFilterMenu="false" style="width: 5%">
                    <template #filter>
                        <InputText
                            v-model="filters.finance_account_number"
                            placeholder="ЛС"
                            @input="onServerFilter('finance_account_number', filters.finance_account_number)"
                            class="p-inputtext p-component"
                            fluid
                        />
                    </template>
                    <template #body="{ data }">
                        {{ data.financeAccount.number }}
                    </template>
                </Column>

                <Column field="financeAccount.balance"  style="width: 10%" header="Баланс" :showFilterMenu="false">
                    <template #filter>
                        <Checkbox v-model="filters.with_debt"
                                  binary
                                  @change="onServerFilter('with_debt', filters.with_debt)"
                                  inputId="with_debt" />
                        <label for="with_debt">  с долгом </label>
                    </template>

                    <template #body="{ data }">
                        {{ data.financeAccount.balance }}
                    </template>
                </Column>

                <Column field="status" header="Статус" style="width: 10%" :showFilterMenu="false">
                    <template #body="{ data }">
                        {{ data.status.label }}
                    </template>
                    <template #filter>
                        <Select
                            :options="apartments.meta.statusOptions"
                            optionLabel="label"
                            optionValue="value"
                            v-model="filters.status"
                            placeholder="Выберите"
                            @change="onServerFilter('status', filters.status)"
                            fluid
                            :showClear="true"
                        />

                    </template>
                </Column>

                <Column field="status" header="Тип" style="width: 10%" :showFilterMenu="false">
                    <template #body="{ data }">
                        {{ data.type.label }}
                    </template>
                    <template #filter>
                        <Select
                            :options="apartments.meta.typeOptions"
                            optionLabel="label"
                            optionValue="value"
                            v-model="filters.type"
                            placeholder="Выберите"
                            @change="onServerFilter('type', filters.type)"
                            fluid
                            :showClear="true"
                        />

                    </template>
                </Column>

                <Column field="building_id" header="Дом" style="width: 20%" :showFilterMenu="false">
                    <template #body="{ data }">
                        {{ data.building?.address || '-' }}
                    </template>
                    <template #filter>
                        <Select
                            :options="buildings.data"
                            optionLabel="address"
                            optionValue="id"
                            v-model="filters.building_id"
                            placeholder="Выберите"
                            @change="onBuildingFilterChange(filters.building_id)"
                            fluid
                            :showClear="true"
                        />
                    </template>
                </Column>

                <Column field="section_id" header="Секция" style="width: 5%" :showFilterMenu="false" :sortable="true">
                    <template #body="{ data }">
                        {{ data.section?.name || '-' }}
                    </template>
                    <template #filter>
                        <Select
                            :options="availableSections"
                            optionLabel="name"
                            optionValue="id"
                            v-model="filters.section_id"
                            placeholder="Выберите"
                            @change="onSectionFilterChange(filters.section_id)"
                            :disabled="!filters.building_id"
                            fluid
                            :showClear="true"
                        />

                    </template>
                </Column>

                <Column field="floor" header="Этаж" :showFilterMenu="false" style="width: 10%" :sortable="true">
                    <template #filter>
                        <InputText
                            v-model="filters.floor"
                            placeholder="Этаж"
                            @input="onServerFilter('floor', filters.floor)"
                            class="p-inputtext p-component"
                            :showClear="true"
                            :disabled="!filters.building_id"
                            fluid
                        />
                    </template>
                </Column>

                <Column field="number" header="Квартира" :showFilterMenu="false" style="width: 10%" :sortable="true">
                    <template #filter>
                        <InputText
                            v-model="filters.number"
                            placeholder="Квартира"
                            @input="onServerFilter('number', filters.number)"
                            class="p-inputtext p-component"
                            :disabled="!filters.building_id"
                            fluid
                        />
                    </template>
                </Column>

<!--                <Column field="created_at" header="Добавлена" bodyStyle="text-align:center"/>-->

                <Column style="width: 15%" header="Действия">
                    <template #body="{ data }">
                        <Button icon="pi pi-gauge" type="button" class="p-button-text" severity="help"
                                @click="openMeterModal(data)"></Button>
                        <Button icon="pi pi-receipt" type="button" class="p-button-text" severity="info"
                                @click="openInvoiceModal(data)"></Button>

                        <Button icon="pi pi-pencil" type="button" class="p-button-text"
                                @click="openEditModal(data)"></Button>
                    </template>
                </Column>

            </DataTable>
        </div>

        <ApartmentModal
            :show.sync="showModal"
            :apartment="currentApartment"
            :buildings="buildings"
            :statusOptions="apartments.meta.statusOptions"
            :typeOptions="apartments.meta.typeOptions"
            :extraServices="extraServices"
            @update:show="val => showModal = val"
        />

        <InvoiceModal
            :show.sync="showInvoiceModal"
            :apartment="currentApartment"

            @update:show="val => showInvoiceModal = val"
        />

        <MeterModal
            :show.sync="showMeterModal"
            :apartment="currentApartment"

            @update:show="val => showMeterModal = val"
        />
    </app-layout>
</template>
