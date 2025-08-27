<script setup>
import {ref, watch} from 'vue';
import {router, useForm} from "@inertiajs/vue3";
import {useToast} from 'primevue/usetoast';

const props = defineProps({
    show: Boolean,
    apartment: {
        type: Object,
        default: null
    }
});

const emit = defineEmits(['update:show', 'refresh']);
const displayModal = ref(false);
const loading = ref(false);
const report = ref([]);
const toast = useToast();

const form = useForm({
    apartment_id: null,

});

watch(() => props.show, async (isOpen) => {
    displayModal.value = isOpen;
    if (isOpen && props.apartment?.id) {
        report.value = [];
        await fetchCalculation();
    }
});

const fetchCalculation = async () => {
    loading.value = true;
    try {
        const {data} = await axios.get(
            route('dashboard.invoices.calculate', {apartment: props.apartment.id})
        );
        report.value = data;
    } finally {
        loading.value = false;
    }
};

const submit = () => {
    form.apartment_id = props.apartment.id;

    form.put(route('dashboard.apartments.create_invoice'), {
        preserveState: true,
        onSuccess: (response) => {
            toast.add({
                severity: 'success',
                summary: 'Успешно',
                detail: 'Проводка создана',
                life: 3000
            });
        },
        onError: () => {
            toast.add({
                severity: 'error',
                summary: 'Ошибка',
                detail: hasError('apartment_id') ? getError('apartment_id') : '',
                life: 3000
            });
        },
        onFinish: () => {
            closeModal();

        },
    });
};

const closeModal = () => {
    displayModal.value = false;
    emit('update:show', false);
    form.reset();
    form.clearErrors();

};

const hasError = (path) => Boolean(form.errors && form.errors[path])
const getError = (path) => (form.errors && form.errors[path]) || ''

</script>

<template>
    <Dialog
        v-model:visible="displayModal"
        modal
        header="Создание проводки"
        :style="{ width: '85rem' }"
        @hide="closeModal"
    >
        <div v-if="loading" class="card flex justify-center">
            <ProgressSpinner/>
        </div>

        <div v-else>
            <div v-for="(ownerData, ownerId) in report.data" :key="ownerId" class="mb-6">
                <h6>{{ ownerData.apartment.building.address }} кв. {{ ownerData.apartment.number }}</h6>

                <DataTable size="small" :value="ownerData.services" showGridlines>
                    <template #header>
                        <b>Плательщик: {{ ownerData.owner.name }} {{ ownerData.owner.phone }}</b>
                    </template>
                    <Column field="service.name" header="Услуга"/>
                    <Column field="tariff.price" header="Тариф"/>
                    <Column field="quantity" header="Кол-во"/>
                    <Column field="sum" header="Сумма"/>
                    <Column field="owner_share" header="К оплате"/>

                    <template #footer>
                        <p> Всего: {{ ownerData.totalSumPrice }} </p>
                        <p> Доля выплаты: {{ ownerData.percent }} </p>
                        <p> К оплате: {{ ownerData.ownerTotal }} </p>

                    </template>

                </DataTable>

                <Divider/>
            </div>

        </div>


        <div class="flex justify-end gap-2 mt-6">
            <Button type="button" label="Отменить" severity="secondary" @click="closeModal"/>
            <Button type="button" label="Создать" @click="submit"/>
        </div>
    </Dialog>
</template>
