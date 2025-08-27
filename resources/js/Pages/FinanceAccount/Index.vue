<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import {Head} from '@inertiajs/vue3';
import { ref, computed } from 'vue'
import {trans} from "@/Utils/translation.utils";
import FinanceAccountModal from "@/Components/Modals/FinanceAccountModal.vue";

const props = defineProps({
    title: String,
    mainAccounts: Object,
    filters: Object

});

const groupedAccounts = computed(() => {
    return props.mainAccounts.data.reduce((acc, account) => {
        const type = account.finance_owner_type;
        if (!acc[type]) acc[type] = [];
        acc[type].push(account);
        return acc;
    }, {});
});

const showModal = ref(false);

const type = ref(null);

const openEditModal = (value) => {
    type.value = value;
    showModal.value = true;
};

</script>

<template>
    <app-layout>
        <Head :title="title"/>

        <div class="card">
            <Toolbar class="mb-6" >
                <template #start>
                    <div class="font-semibold text-xl">Управление счетами</div>
                </template>
            </Toolbar>

            <div class="space-y-10">
                <div v-for="(accounts, type) in groupedAccounts" :key="type">
                    <h5 class="text-lg font-semibold text-gray-700 dark:text-gray-200 mb-6">
                        {{ trans('finance.' + type) }}
                        <Button icon="pi pi-plus" class="ml-2" label="Добавить" severity="secondary" variant="outlined"  size="small" @click="openEditModal(type)"/>
                    </h5>

                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                        <div v-for="account in accounts" :key="account.id" class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-5 flex flex-col justify-between">
                            <div class="mb-4">
                                <span class="text-gray-500 dark:text-gray-400 font-medium block mb-1">{{ account.name }}</span>
                                <div class="text-gray-900 dark:text-gray-100 font-semibold text-xl">#{{ account.number }}</div>
                            </div>

                            <div class="flex justify-between items-center mt-auto">
                                <div class="text-green-600 dark:text-green-400 font-semibold text-lg">
                                    {{ account.balance }} грн.
                                </div>
                                <div class="text-gray-600 dark:text-gray-300 text-sm space-y-1">
                                    <div>Дебет: {{ account.debit }} грн.</div>
                                    <div>Кредит: {{ account.credit }} грн.</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
       </div>

        <FinanceAccountModal
            :show.sync="showModal"
            :type="type"
            :financeOwnerTypeOption="mainAccounts.meta.financeOwnerTypeOption"
            :companyListOptions="mainAccounts.meta.companyListOptions"
            :thirdPartyListOptions="mainAccounts.meta.thirdPartyListOptions"
            @update:show="val => showModal = val"
        />

    </app-layout>
</template>

