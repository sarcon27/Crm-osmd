<script setup>
import {ref, watch} from 'vue';
import {router, useForm} from "@inertiajs/vue3";
import {useToast} from 'primevue/usetoast';

const props = defineProps({
    show: Boolean,
    transaction: {
        type: Object,
        default: null
    }
});

const emit = defineEmits(['update:show', 'refresh']);
const displayModal = ref(false);

watch(() => props.show, async (isOpen) => {
    displayModal.value = isOpen;
});

const closeModal = () => {
    displayModal.value = false;
    emit('update:show', false);
};

function formatDate(v) {
    if (!v) return '—'
    const d = new Date(v)
    if (String(d) === 'Invalid Date') return v
    return new Intl.DateTimeFormat('uk-UA', {dateStyle: 'medium', timeStyle: 'short'}).format(d)
}

function money(v) {
    const n = Number(v)
    return new Intl.NumberFormat('uk-UA', {style: 'currency', currency: 'UAH'}).format(n)
}

const getStatusClass = (status) => {
    switch (status) {
        case 'opened':
            return 'warn';
        case 'rejected':
            return 'danger';
        default:
            return 'success';
    }
};

</script>

<template>
    <Dialog
        v-model:visible="displayModal"
        modal
        :header="'Проводка #' + transaction?.id"
        :style="{ width: '85rem' }"
        @hide="closeModal">
        <!--        <pre>-->
        <!--        {{transaction}}-->
        <!--        </pre>-->


        <div class="px-4 space-y-4">

            <span
                class="text-surface-900 dark:text-surface-0 font-semibold text-2xl! leading-tight!">{{ transaction.name }}</span>

            <div class="bg-surface-50 dark:bg-surface-950 py-4">
                <div class="grid grid-cols-1 gap-8 md:grid-cols-2 lg:grid-cols-3">
                    <div class="bg-surface-0 dark:bg-surface-900 shadow-sm p-5 rounded-2xl">
                        <div class="flex justify-between gap-4">
                            <div class="flex flex-col gap-3">
                                <span
                                    class="text-surface-700 dark:text-surface-300 font-normal leading-tight">Дебет</span>
                                <div
                                    class="text-surface-900 dark:text-surface-0 font-semibold text-2xl! leading-tight!">
                                    {{ transaction.debit_account?.number }}
                                </div>
                            </div>
                            <div class="flex items-center justify-center w-10 h-10">
                                <i class="pi pi-arrow-up text-surface-0 dark:text-surface-900 text-xl! leading-none!"/>
                            </div>
                        </div>
                        <div class="mt-4">
                            <span
                                class="text-surface-600 dark:text-surface-300 font-medium leading-tight">{{
                                    transaction.debit_account?.name
                                }}</span>
                            <div class="text-surface-500 dark:text-surface-300 leading-tight mt-1">
                                <template v-if="transaction.debit_account.finance_owner_type === 'apartment'">
                                    {{ transaction.debit_account?.financeOwner?.building?.address }}
                                    кв.{{ transaction.debit_account?.financeOwner?.number }}
                                </template>
                                <template v-else>
                                    {{ transaction.debit_account?.financeOwner?.name }}
                                </template>
                            </div>

                        </div>
                    </div>

                    <div class="bg-surface-0 dark:bg-surface-900 shadow-sm p-5 rounded-2xl">
                        <div class="flex justify-between gap-4">
                            <div class="flex flex-col gap-3">
                                <span
                                    class="text-surface-700 dark:text-surface-300 font-normal leading-tight">Кредит</span>
                                <div
                                    class="text-surface-900 dark:text-surface-0 font-semibold text-2xl! leading-tight!">
                                    {{ transaction.credit_account?.number }}
                                </div>
                            </div>
                            <div class="flex items-center justify-center w-10 h-10">
                                <i class="pi pi-arrow-down text-surface-0 dark:text-surface-900 text-xl! leading-none!"/>
                            </div>
                        </div>
                        <div class="mt-4">
                            <span
                                class="text-surface-600 dark:text-surface-300 font-medium leading-tight">{{
                                    transaction.credit_account?.name
                                }}</span>
                            <div class="text-surface-500 dark:text-surface-300 leading-tight mt-1">
                                <template v-if="transaction.credit_account.finance_owner_type === 'apartment'">
                                    {{ transaction.credit_account?.financeOwner?.building?.address }}
                                    кв.{{ transaction.credit_account?.financeOwner?.number }}
                                </template>
                                <template v-else>
                                    {{ transaction.credit_account?.financeOwner?.name }}
                                </template>
                            </div>

                        </div>
                    </div>

                    <div class="bg-surface-0 dark:bg-surface-900 shadow-sm p-5 rounded-2xl">
                        <div class="flex justify-between gap-4">
                            <div class="flex flex-col gap-2">
                                <span
                                    class="text-surface-700 dark:text-surface-300 font-normal leading-tight">Сумма</span>
                                <div
                                    class="text-surface-900 dark:text-surface-0 font-semibold text-2xl! leading-tight!">
                                    {{ money(transaction.total) }}
                                </div>
                            </div>
                            <div class="flex items-center justify-center w-25 h-10">
                                <Badge size="xlarge" :severity="getStatusClass(transaction.status.value)"
                                       :value="transaction.status.label"></Badge>
                            </div>
                        </div>
                        <div class="mt-4">
                            <span
                                class="text-surface-500 dark:text-surface-300 leading-tight"> Создана: {{
                                    transaction.created_at
                                }}</span>
                            <div class="text-surface-500 dark:text-surface-300 leading-tight mt-1"> Проведена:
                                {{ transaction.posted_at ?? '-' }}
                            </div>
                        </div>
                    </div>

                </div>
            </div>


            <div class="grid  gap-4">
                <div class="">
                    <Card>
                        <template #title>
                            <div class="flex items-center gap-2">
                                <i class="pi pi-list"></i>
                                <span class="text-lg ">Детально</span>
                            </div>
                        </template>
                        <template #content>
                            <DataTable :value="transaction.entries" dataKey="id" class="p-datatable-sm">
                                <Column field="id" header="ID"/>
                                <Column field="amount" header="Сумма"/>
                                <Column field="notes" header="Примечание"/>

                                <Column header="Документ">
                                    <template #body="{ data }">
                                        <div v-if="data.invoice">
                                            <span class="font-medium">#{{ data.invoice?.id }}</span>
                                            <span class="text-xss text-gray-500">
                                                Период: {{ data.invoice?.period }}
                                                Сумма: {{ data.invoice?.total }}
                                            </span>

                                            <DataTable :value="data.invoice?.items" dataKey="id"
                                                       class="mt-2 p-datatable-sm">
                                                <Column field="id" header="#" style="width:50px"/>
                                                <Column header="Услуга">
                                                    <template #body="{ data: item }">
                                                        {{ item.service?.name }}
                                                    </template>
                                                </Column>
                                                <Column field="quantity" header="Кол-во" style="width:100px"/>
                                                <Column field="amount" header="Сумма" style="width:120px"/>
                                            </DataTable>
                                        </div>
                                    </template>

                                </Column>
                            </DataTable>

                        </template>
                    </Card>
                </div>
            </div>
        </div>

    </Dialog>
</template>

<style scoped>
</style>
