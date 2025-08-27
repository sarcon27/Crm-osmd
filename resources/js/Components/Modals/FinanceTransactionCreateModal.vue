<script setup>
import {useResourceForm} from '@/composables/useResourceFormSingle';
import {ref} from "vue";

const props = defineProps({
    show: Boolean,
});

const emit = defineEmits(['update:show', 'refresh']);
const searchDebitAccountResults = ref([])
const searchCreditAccountResults = ref([])

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
    }
})

const searchAccount = async (event, type) => {
    try {
        const res = await axios.get(route('dashboard.finance-accounts.search'), {
            params: { q: event.query }
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


</script>

<template>
    <Dialog
        v-model:visible="displayModal"
        modal
        header="Добавление проводки"
        :style="{ width: '55rem' }"
        @hide="closeModal">


        <div class="grid grid-cols-12 gap-2 mb-4">
            <div class="col-span-10">

                <AutoComplete
                    v-model="form.debit_account_id"
                    :suggestions="searchDebitAccountResults.data"
                    @complete="(e) => searchAccount(e, 'debit')"
                    field="id"
                    placeholder="Дебил ЛС"
                    option-label="name"
                    class="w-62"
                />
                <Message v-if="hasError('debit_account_id')" severity="error" size="small" variant="simple" class="mt-1">
                    {{ getError('debit_account_id') }}
                </Message>
            </div>
        </div>

        <div class="grid grid-cols-12 gap-2 mb-4">
            <div class="col-span-10">
                <AutoComplete
                    v-model="form.credit_account_id"
                    :suggestions="searchCreditAccountResults.data"
                    @complete="(e) => searchAccount(e, 'credit')"
                    field="id"
                    placeholder="Кредит ЛС"
                    option-label="name"
                    class="w-62"
                />

                <Message v-if="hasError('credit_account_id')" severity="error" size="small" variant="simple" class="mt-1">
                    {{ getError('credit_account_id') }}
                </Message>
            </div>
        </div>

        <div class="grid grid-cols-12 gap-2 mb-4">
            <div class="col-span-4">
                <div class="grid grid-cols-12 gap-2">
                    <div class="col-span-6">
                        <InputText v-model="form.total" class="w-full" :invalid="hasError('total')"  placeholder="0.00"/>
                        <Message v-if="hasError('total')" severity="error" size="small" variant="simple" class="mt-1">
                            {{ getError('total') }}
                        </Message>
                    </div>
                </div>
            </div>
        </div>



    </Dialog>
</template>

<style scoped>
</style>
