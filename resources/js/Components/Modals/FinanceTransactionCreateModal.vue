<script setup>
import {useResourceForm} from '@/composables/useResourceFormSingle';
import {computed, ref} from "vue";

const props = defineProps({
    show: Boolean,
});

const emit = defineEmits(['update:show', 'refresh']);
const searchDebitAccountResults = ref([])
const searchCreditAccountResults = ref([])
const selectedDebitAccount = ref(null);
const selectedCreditAccount = ref(null);

const {
    displayModal,
    closeModal,
    form,
    hasError,
    getError,
    submit
} = useResourceForm(props, emit, {
    resourceName: 'finance-transactions',
    propKey: null,
    defaultFormData: {
        debit_account_id: '',
        credit_account_id: '',
        total: '',
        name: '',

    },
    onInitData: (data, form) => {
        if (!data) return

        Object.assign(form, {
            ...data,
            status: data?.status?.value ?? '',
            type: data?.type?.value ?? ''
        })
    },
    resetRefs: [selectedDebitAccount, selectedCreditAccount]

})

const searchAccount = async (event, type) => {
    try {
        const res = await axios.get(route('dashboard.finance-accounts.search'), {
            params: {q: event.query}
        })

        if (type === 'debit') {
            searchDebitAccountResults.value = res.data
        } else if (type === 'credit') {
            searchCreditAccountResults.value = res.data
        }
    } catch (e) {
        console.error(e)
    }
}

const debitAccountModel = computed({
    get() {
        return selectedDebitAccount.value;
    },
    set(account) {
        selectedDebitAccount.value = account;
        form.debit_account_id = account ? account.id : null;
    }
});

const creditAccountModel = computed({
    get() {
        return selectedCreditAccount.value;
    },
    set(account) {
        selectedCreditAccount.value = account;
        form.credit_account_id = account ? account.id : null;
    }
});

</script>

<template>
    <Dialog
        v-model:visible="displayModal"
        modal
        header="Добавление проводки"
        :style="{ width: '35rem' }"
        @hide="closeModal">

        <div class="space-y-6">
            <div class="flex items-center gap-4">
                <label class="font-semibold w-24">Дебет</label>
                <div class="flex-1">
                    <AutoComplete
                        v-model="debitAccountModel"
                        :suggestions="searchDebitAccountResults.data"
                        :invalid="hasError('debit_account_id')"
                        @complete="(e) => searchAccount(e, 'debit')"
                        field="id"
                        placeholder="Введите номер ЛС"
                        option-label="formatName"
                        class="w-full"
                        fluid
                        loader="pi pi-refresh"
                        emptySearchMessage="Не найдено"
                    />
                    <Message
                        v-if="hasError('debit_account_id')"
                        severity="error"
                        size="small"
                        variant="simple"
                        class="mt-1"
                    >
                        {{ getError('debit_account_id') }}
                    </Message>
                </div>
            </div>

            <div class="flex items-center gap-4">
                <label class="font-semibold w-24">Кредит</label>
                <div class="flex-1">
                    <AutoComplete
                        v-model="creditAccountModel"
                        :suggestions="searchCreditAccountResults.data"
                        :invalid="hasError('credit_account_id')"
                        @complete="(e) => searchAccount(e, 'credit')"
                        field="id"
                        placeholder="Введите номер ЛС"
                        option-label="formatName"
                        class="w-full"
                        fluid
                        loader="pi pi-refresh"
                        emptySearchMessage="Не найдено"
                    />
                    <Message
                        v-if="hasError('credit_account_id')"
                        severity="error"
                        size="small"
                        variant="simple"
                        class="mt-1"
                    >
                        {{ getError('credit_account_id') }}
                    </Message>
                </div>
            </div>

            <div class="flex items-center gap-4">
                <label class="font-semibold w-24">Сумма</label>
                <div class="flex-1">
                    <InputText
                        v-model="form.total"
                        :invalid="hasError('total')"
                        placeholder="0.00"
                        class=""
                    />
                    <Message
                        v-if="hasError('total')"
                        severity="error"
                        size="small"
                        variant="simple"
                        class="mt-1"
                    >
                        {{ getError('total') }}
                    </Message>
                </div>
            </div>


            <div class="flex items-center gap-4">
                <label class="font-semibold w-24">Название</label>
                <div class="flex-1">
                    <InputText
                        v-model="form.name"
                        :invalid="hasError('name')"
                        placeholder="Название операции"
                        class="w-full"
                    />
                    <Message
                        v-if="hasError('name')"
                        severity="error"
                        size="small"
                        variant="simple"
                        class="mt-1"
                    >
                        {{ getError('name') }}
                    </Message>
                </div>
            </div>
        </div>

        <Divider/>

        <div class="flex justify-end gap-2">
            <Button type="button" label="Отменить" severity="secondary" @click="closeModal"/>
            <Button type="button" label="Сохранить" :loading="form.processing" @click="submit"/>
        </div>

    </Dialog>
</template>

<style scoped>
</style>
