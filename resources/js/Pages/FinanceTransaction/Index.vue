<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import {Head, router, useForm} from '@inertiajs/vue3';
import {ref, reactive, computed} from 'vue';
import {trans} from "@/Utils/translation.utils";
import FinanceTransactionViewModal from "@/Components/Modals/FinanceTransactionViewModal.vue";
import {useConfirm} from "primevue/useconfirm";
import FinanceTransactionCreateModal from "@/Components/Modals/FinanceTransactionCreateModal.vue";

const props = defineProps({
    title: String,
    transactions: Object,
    filters: Object,

});

const form = useForm({});

const filters = reactive({});
const confirm = useConfirm();

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

    router.get(route('dashboard.finance-transactions.index'), query, {
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
    const link = props.transactions.meta.links.find(l => l.label == targetPage);

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
    router.get(route('dashboard.finance-transactions.index'), {
        sort: sortOrder,
        page: props.transactions.meta.current_page,
        per_page: props.transactions.meta.per_page
    }, {
        preserveScroll: true,
        preserveState: true
    });
};

const showModal = ref(false);
const showCreateModal = ref(false);

const currentTransaction = ref(null);

const openShowModal = (transaction) => {
    currentTransaction.value = transaction;
    showModal.value = true;
};

const openCreateModal = () => {
    showCreateModal.value = true;
};

const postTransaction = (id) => {
    confirm.require({
        message: 'Провести? Проведенные проводки отменить будет нельзя',
        header: 'Подтверждение',
        icon: 'pi pi-exclamation-triangle',
        rejectProps: {
            label: 'Отмена',
            severity: 'secondary',
            outlined: true
        },
        acceptProps: {
            label: 'Провести'
        },
        accept: () => {
            form.post(route('dashboard.finance.transactions.post', id), {
                preserveScroll: true,
            });
        },
    });
};

const rejectTransaction = (id) => {
    confirm.require({
        message: 'Данная операция пометит проводку как удаленную',
        header: 'Подтверждение',
        icon: 'pi pi-exclamation-triangle',
        rejectProps: {
            label: 'Отмена',
            severity: 'danger',
            outlined: true
        },
        acceptProps: {
            label: 'Удалить'
        },
        accept: () => {
            form.post(route('dashboard.finance.transactions.reject', id), {
                preserveScroll: true,
            });
        },
    });
};

</script>

<template>
    <app-layout>
        <Head :title="title"/>

        <div class="card">
            <Toolbar class="mb-6">
                <template #start>
                    <div class="font-semibold text-xl">Проводки</div>
                </template>
                <template #end>
                    <Button icon="pi pi-plus" label="Добавить"  @click="openCreateModal()"/>
                </template>
            </Toolbar>

            <DataTable
                :value="transactions.data"
                :rows="transactions.meta.per_page"
                :totalRecords="transactions.meta.total"
                :paginator="transactions.meta.total > transactions.meta.per_page"
                :lazy="true"
                filterDisplay="row"
                resizableColumns
                columnResizeMode="fit"
                responsiveLayout="scroll"
                @page="onPage"
                @sort="onSort"

            >
                <template #empty> Не найдено.</template>
                <Column
                    header="#"
                    bodyStyle="text-align:center"
                >
                    <template #body="{ index }">
                        {{ (transactions.meta.current_page - 1) * transactions.meta.per_page + index + 1 }}
                    </template>
                </Column>


                <Column field="status" header="Статус" style="width: 10%" :showFilterMenu="false">
                    <template #body="{ data }">
                        {{ data.status.label }}
                    </template>
                    <template #filter>
                        <Select
                            :options="transactions.meta.statusOptions"
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

                <Column field="name" header="Назначение"/>

                <Column field="debit_account" header="Дебит" :showFilterMenu="false">
                    <template #body="{ data }">
                        {{ data.debit_account.number }}
                    </template>
                    <template #filter>
                        <InputText
                            v-model="filters.debit_account_number"
                            placeholder="Номер ЛС"
                            @input="onServerFilter('debit_account_number', filters.debit_account_number)"
                            class="p-inputtext p-component"
                            :showClear="true"
                        />
                        <Button
                            v-if="filters.debit_account_number"
                            icon="pi pi-times"
                            class="p-button-text"
                            @click="clearFilter('debit_account_number')"
                        />
                    </template>
                </Column>

                <Column field="credit_account" header="Кредит" :showFilterMenu="false">
                    <template #body="{ data }">
                        {{ data.credit_account.number }}
                    </template>
                    <template #filter>
                        <InputText
                            v-model="filters.credit_account_number"
                            placeholder="Номер ЛС"
                            @input="onServerFilter('credit_account_number', filters.credit_account_number)"
                            class="p-inputtext p-component"
                            :showClear="true"
                        />
                        <Button
                            v-if="filters.credit_account_number"
                            icon="pi pi-times"
                            class="p-button-text"
                            @click="clearFilter('credit_account_number')"
                        />
                    </template>
                </Column>

                <Column field="total" header="Сумма"/>

                <Column field="posted_at" header="Проведена" :showFilterMenu="false">
                    <template #body="{ data }">
                        <span v-if="!data.posted_at && data.status.value !== 'rejected'">
                            <Button icon="pi pi-check" type="button" class="p-button-text"
                                    @click="postTransaction(data.id)"></Button>
                            <Button icon="pi pi-times" type="button" class="p-button-text" severity="danger"
                                    @click="rejectTransaction(data.id)"></Button>
                        </span>

                        <Badge v-else-if="data.status.value === 'rejected'" :value="data.status.label" severity="danger"></Badge>
                        <Badge v-else :value="data.posted_at" severity="success"></Badge>
                    </template>
                </Column>

                <Column field="created_at" header="Создана"/>

                <Column header="">
                    <template #body="{ data }">
                        <Button icon="pi pi-eye" size="" type="button" class="p-button-text" @click="openShowModal(data)"></Button>
                    </template>
                </Column>

            </DataTable>
        </div>

        <ConfirmDialog />

        <FinanceTransactionViewModal
            :show.sync="showModal"
            :transaction="currentTransaction"
            @update:show="val => showModal = val"
        />
        <FinanceTransactionCreateModal
            :show.sync="showCreateModal"
            @update:show="val => showCreateModal = val"
        />
    </app-layout>
</template>
