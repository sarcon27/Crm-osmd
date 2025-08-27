<script setup>
import {useForm} from "@inertiajs/vue3";
import {ref, watch} from 'vue';
import {useToast} from 'primevue/usetoast';

const props = defineProps({
    show: Boolean,
    account: null,
    type: String,
    financeOwnerTypeOption: Object,
    companyListOptions: Object,
    thirdPartyListOptions: Object
});



const emit = defineEmits(['update:show', 'refresh']);
const displayModal = ref(false);
const toast = useToast();


const form = useForm({
    id: null,
    number: '',
    name: '',
    finance_owner_type: null,
    finance_owner_id: null
});


watch(() => props.show, (value) => {
    displayModal.value = value;

    if (value) {
        form.reset();
        form.clearErrors();
        form.finance_owner_type = props.type ?? null;
    }
});


const submit = () => {
    const url = route('dashboard.finance-accounts.store');
    const method = 'post';

    form.transform(data => ({
        ...data,
    }))[method](url, {
        preserveScroll: true,
        onSuccess: () => {
            toast.add({
                severity: 'success',
                summary: 'Успешно',
                detail: form.id ? 'Сохранено' : 'Добавлено',
                life: 3000
            });
            closeModal();
        },
        onError: () => {
            toast.add({
                severity: 'error',
                summary: 'Error',
                detail: 'Проверьте поля формы',
                life: 3000
            });
        }
    });
};

const closeModal = () => {
    displayModal.value = false;
    emit('update:show', false);
    form.reset();
    form.clearErrors()
};

const hasError = (path) => Boolean(form.errors && form.errors[path])
const getError = (path) => (form.errors && form.errors[path]) || ''

</script>

<template>
    <Dialog v-model:visible="displayModal"
            modal
            :header="form.id ? 'Редактирование' : 'Добавление счета'"
            :style="{ width: '50rem' }"
            @hide="closeModal">

        <div class="grid grid-cols-12 gap-2 mb-4">
            <label class="col-span-2 font-semibold self-center">Номер</label>
            <div class="col-span-10">
                <InputText v-model="form.number" class="w-full" :invalid="hasError('number')"/>
                <Message v-if="hasError('number')" severity="error" size="small" variant="simple" class="mt-1">
                    {{ getError('number') }}
                </Message>
            </div>
        </div>

        <div class="grid grid-cols-12 gap-2 mb-4">
            <label class="col-span-2 font-semibold self-center">Название</label>
            <div class="col-span-10">
                <InputText v-model="form.name" class="w-full" :invalid="hasError('name')"/>
                <Message v-if="hasError('name')" severity="error" size="small" variant="simple" class="mt-1">
                    {{ getError('name') }}
                </Message>
            </div>
        </div>

        <div class="grid grid-cols-12 gap-4 mt-4 mb-4">
            <label class="col-span-2 font-semibold">Владелец счета</label>
            <div class="col-span-8">
                <Select
                    :options="type === 'company' ? companyListOptions : thirdPartyListOptions "
                    optionLabel="name"
                    optionValue="id"
                    v-model="form.finance_owner_id"
                    placeholder="Выберите"
                    :invalid="hasError('finance_owner_id')"
                    fluid
                />
                <Message v-if="hasError('finance_owner_id')" severity="error" size="small" variant="simple"
                         class="mt-1">
                    {{ getError('finance_owner_id') }}
                </Message>
            </div>
        </div>

        <div class="flex justify-end gap-2 mt-6">
            <Button type="button" label="Отменить" severity="secondary" @click="closeModal"/>
            <Button type="button" label="Сохранить" :disabled="!form.isDirty" :loading="form.processing"
                    @click="submit"/>
        </div>
    </Dialog>

</template>
