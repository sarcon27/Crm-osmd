<script setup>
import {useResourceForm} from '@/composables/useResourceFormSingle';
import {ref, onMounted, watch, computed} from 'vue';

const props = defineProps({
    show: Boolean,
    apartment: Object
});

const emit = defineEmits(['update:show', 'refresh']);
const services = ref([]);
const loading = ref(false);

const fetchServices = async () => {
    loading.value = true;
    try {
        const {data} = await axios.get(
            route('dashboard.apartments.meter-services', {apartment: props.apartment.id})
        );
        services.value = data;
    } finally {
        loading.value = false;
    }
};

const {
    displayModal,
    closeModal,
    form,
    hasError,
    getError,
    submit
} = useResourceForm(props, emit, {
    resourceName: 'meters',
    propKey: null,

    defaultFormData: {
        new_value: '',
        service_id: null,
        apartment_id: null,
        is_next_period: false
    },
})

const selectedServiceMeasure = computed(() => {
    if (!form.service_id) return '';
    const service = services.value.data.find(s => s.id === form.service_id);
    return service?.tariff?.measure?.name || '';
});

watch(() => props.show, async (isOpen) => {
    displayModal.value = isOpen;
    if (isOpen && props.apartment?.id) {
        form.apartment_id = props.apartment.id;
        services.value = [];
        await fetchServices();
    }
});


</script>

<template>
    <Dialog v-model:visible="displayModal"
            modal
            header="Добавление показаний счетчика"
            :style="{ width: '50rem' }"
            @hide="closeModal">

        <div class="grid grid-cols-12 gap-2 mb-4">
            <label class="col-span-2 font-semibold self-center">Услуга</label>
            <div class="col-span-4">
                <Select
                    :options="services.data"
                    optionLabel="name"
                    optionValue="id"
                    v-model="form.service_id"
                    placeholder="Услуга"
                    fluid
                    :invalid="hasError('service_id')"
                />
                <Message v-if="hasError('service_id')" severity="error" size="small" variant="simple" class="mt-1">
                    {{ getError('service_id') }}
                </Message>
            </div>
        </div>

        <div class="grid grid-cols-12 gap-2 mb-4">
            <label class="col-span-2 font-semibold self-center">Значение</label>
            <div class="col-span-4">
                <InputGroup>
                    <InputText placeholder="Показание счетчика" v-model="form.new_value"
                               :invalid="hasError('new_value')"/>
                    <InputGroupAddon>{{selectedServiceMeasure}}</InputGroupAddon>
                </InputGroup>
                <Message v-if="hasError('new_value')" severity="error" size="small" variant="simple" class="mt-1">
                    {{ getError('new_value') }}
                </Message>
            </div>
        </div>

        <div class="grid grid-cols-12 gap-2 mb-4">
            <label class="col-span-2 font-semibold self-center">В следующий период</label>
            <div class="col-span-4">
                 <Checkbox  v-model="form.is_next_period" binary/>
            </div>
        </div>

        <div class="flex justify-end gap-2 mt-6">
            <Button type="button" label="Отменить" severity="secondary" @click="closeModal"/>
            <Button type="button" label="Сохранить" :disabled="!form.isDirty" :loading="form.processing"
                    @click="submit"/>
        </div>
    </Dialog>

</template>
