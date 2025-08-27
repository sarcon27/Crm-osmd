<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import {Head, router} from '@inertiajs/vue3';
import { ref } from 'vue'
import InvoiceViewModal from "@/Components/Modals/InvoiceViewModal.vue";

const props = defineProps({
    title: String,
    invoices: Object,
    filters: Object

});

const onPage = (event) => {
    const targetPage = event.page + 1;
    const link = props.invoices.meta.links.find(l => l.label == targetPage);

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
    router.get(route('dashboard.invoices.index'), {
        sort: sortOrder,
        page: props.invoices.meta.current_page,
        per_page: props.invoices.meta.per_page
    }, {
        preserveScroll: true,
        preserveState: true
    });
};

const onServerFilter = (field, value) => {
    router.get(route('dashboard.invoices.index'), {
        ...props.filters,
        [`filter[${field}]`]: value,
        page: 1
    }, {
        preserveScroll: true,
        preserveState: true
    });
};

const showModal = ref(false);

const currentInvoice = ref(null);

const periodFilter = ref(null);

const formatPeriod = (date) => {
    if (!date) return "";
    const year = date.getFullYear();
    const month = String(date.getMonth() + 1).padStart(2, "0");
    return `${year}-${month}`;
};

const openViewModal = (building) => {
    currentInvoice.value = building;
    showModal.value = true;
};

</script>

<template>
    <app-layout>
        <Head :title="title"/>

        <div class="card">
            <Toolbar class="mb-6" >
                <template #start>
                    <div class="font-semibold text-xl">Документы</div>
                </template>
            </Toolbar>

            <DataTable
                :value="invoices.data"
                :rows="invoices.meta.per_page"
                :totalRecords="invoices.meta.total"
                :paginator="true"
                :lazy="true"
                filterDisplay="row"
                resizableColumns
                columnResizeMode="fit"
                responsiveLayout="scroll"
                @page="onPage"
                @sort="onSort"

            >
                <template #empty> Не найдено. </template>

                <Column
                    header="#"
                    bodyStyle="text-align:center"
                >
                    <template #body="{ index }">
                        {{ (invoices.meta.current_page - 1) * invoices.meta.per_page + index + 1 }}
                    </template>
                </Column>

                <Column field="financeAccount.number" header="ЛС" :showFilterMenu="false" style="width: 10%">
                    <template #filter>
                        <InputText
                            v-model="filters.finance_account_number"
                            placeholder="Поиск по ЛС"
                            @input="onServerFilter('finance_account_number', filters.finance_account_number)"
                            class="p-inputtext p-component"
                            fluid
                        />
                        <Button  v-if="props.filters?.filter?.finance_account_number" icon="pi pi-times" class="p-button-text"
                                 @click="onServerFilter('finance_account_number', '')"
                        />
                    </template>
                    <template #body="{ data }">
                        {{ data.apartment?.financeAccount?.number }}
                    </template>
                </Column>

                <Column field="owner" header="Плательщик"  style="width: 20%" :showFilterMenu="false">
                    <template #body="{ data }">
                        {{ data.owner.name }}
                    </template>
                    <template #filter>
                        <InputText
                            :value="props.filters?.filter?.owner || ''"
                            @input="e => onServerFilter('owner', e.target.value)"
                            placeholder="Поиск по ФИО"
                            class="p-inputtext p-component"
                            fluid
                        />
                        <Button  v-if="props.filters?.filter?.owner" icon="pi pi-times" class="p-button-text"
                                 @click="onServerFilter('owner', '')"
                        />
                    </template>
                </Column>


                <Column field="address" header="Адрес"  style="width: 25%" :showFilterMenu="false">
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

                <Column field="number" header="Квартира" style="width: 10%"  :showFilterMenu="false" bodyStyle="text-align:center" >
                    <template #body="{ data }">
                        {{ data.apartment?.number }}
                    </template>
                    <template #filter>
                        <InputText
                            :value="props.filters?.filter?.number || ''"
                            @input="e => onServerFilter('number', e.target.value)"
                            placeholder="Поиск по квартире"
                            class="p-inputtext p-component"
                            fluid
                        />
                        <Button  v-if="props.filters?.filter?.number" icon="pi pi-times" class="p-button-text"
                                 @click="onServerFilter('number', '')"
                        />
                    </template>
                </Column>

                <Column field="period" header="Период" style="width: 15%" bodyStyle="text-align:center" :showFilterMenu="false">
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
                            fluid
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

                <Column field="number" header="Проводка" style="width: 10%"  :showFilterMenu="false" bodyStyle="text-align:center" >
                    <template #body="{ data }">
                        {{ data.transactionEntry?.transaction?.status }}
                    </template>

                </Column>

                <Column field="total" header="Всего" bodyStyle="text-align:center" :sortable="true"/>
                <Column field="debt" header="Долг" bodyStyle="text-align:center" :sortable="true"/>
                <Column field="created_at" header="Создан" bodyStyle="text-align:center"/>

                <Column header="">
                    <template #body="{ data }">
                        <Button icon="pi pi-eye" type="button" class="p-button-text"
                                @click="openViewModal(data)"></Button>

                    </template>

                </Column>

            </DataTable>
        </div>

        <InvoiceViewModal
            :show.sync="showModal"
            :invoice="currentInvoice"

            @update:show="val => showModal = val"
        />
    </app-layout>
</template>
