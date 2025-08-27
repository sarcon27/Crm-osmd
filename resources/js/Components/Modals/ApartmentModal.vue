<script setup>
import {ref, computed, watch} from 'vue';
import {useForm, router} from '@inertiajs/vue3';
import Empty from "@/Pages/Empty.vue";

const props = defineProps({
    show: Boolean,
    apartment: Object,
    buildings: Array,
    statusOptions: Array,
    typeOptions: Array,
    extraServices: Object
});

const emit = defineEmits(['update:show', 'refresh']);
const displayModal = ref(false);
const showNewForm = ref(false);
const selectedOwner = ref(null)
const searchResults = ref([])

const form = useForm({
    id: null,
    building_id: null,
    section_id: null,
    number: '',
    floor: 1,
    total_area: '',
    living_area: '',
    rooms_count: '',
    status: '',
    type: '',
    notes: '',
    registered_residents: 1,
    owners: [],
    extraServices: [],
});

// Доступные секции для выбранного дома
const availableSections = computed(() => {
    const building = props.buildings.data.find(b => b.id === form.building_id);
    return building ? building.sections : [];
});

watch(() => props.show, value => {
    displayModal.value = value;
    if (value) {
        if (props.apartment) {
            Object.assign(form, props.apartment);
            form.status = props.apartment.status?.value ?? '';
            form.type = props.apartment.type?.value ?? '';
            form.extraServices = props.apartment.extraServices.map(s => s.id);
        } else {
            form.reset();
        }
    }
});

const getDefaultPaymentPercent = () => !form.owners.length ? '100.00' : '0.00';

const addOwner = (owner) => {
    if (!owner) return;

    const exists = owner.id ? form.owners.some(o => o.id === owner.id) : false;
    if (!exists) {
        form.owners.push({
            ...owner,
            id: owner.id ?? null,
            is_payer: false,
            payment_percent: getDefaultPaymentPercent()
        });
    }
};

const addNewOwner = () => {
    if (!newOwner.value.name) return;

    addOwner(newOwner.value);
    newOwner.value = { name: '', phone: '', email: '' };
};

const addOwnerFromSelect = () => {
    if (!selectedOwner.value) return;

    addOwner(selectedOwner.value);
    selectedOwner.value = null;
};

const newOwner = ref({
    name: '',
    phone: '',
    email: ''
})

const removeOwner = (id) => {
    form.owners = form.owners.filter(o => o.id !== id)
}

const searchOwners = async (event) => {
    try {
        console.log(event.query)
        const res = await axios.get(route('dashboard.owners.search'), {
            params: {q: event.query}
        })
        searchResults.value = res.data

    } catch (e) {
        console.error(e)
    }
}

const submit = () => {
    const url = form.id
        ? route('dashboard.apartments.update', form.id)
        : route('dashboard.apartments.store');

    const method = form.id ? 'put' : 'post';

    form[method](url, {
        preserveScroll: true,
        onSuccess: () => {
            emit('refresh');
            displayModal.value = false;
        }
    });
};
const closeModal = () => {
    displayModal.value = false;
    emit('update:show', false);
    form.reset();
    form.clearErrors();
    showNewForm.value = false;
};

const hasError = (path) => Boolean(form.errors && form.errors[path])
const getError = (path) => (form.errors && form.errors[path]) || ''

</script>

<template>
    <Dialog
        v-model:visible="displayModal"
        modal
        :header="form.id ? 'Редактирование квартиры ЛС ' + apartment.financeAccount?.number : 'Добавление квартиры'"
        :style="{ width: '65rem' }"
        @hide="closeModal"
    >
        <div class="grid grid-cols-12 gap-4 mb-4">
            <div class="col-span-6">
                <div class="grid grid-cols-12 gap-2 items-center">
                    <label class="col-span-2 font-semibold">Адрес</label>
                    <div class="col-span-10">
                        <Select
                            :options="buildings.data"
                            optionLabel="address"
                            optionValue="id"
                            v-model="form.building_id"
                            placeholder="Выберите"
                            fluid
                            :invalid="hasError('building_id')"
                        />
                        <Message v-if="hasError('building_id')" severity="error" size="small" variant="simple"
                                 class="mt-1">
                            {{ getError('building_id') }}
                        </Message>
                    </div>
                </div>
            </div>
            <div class="col-span-6">
                <div class="grid grid-cols-12 gap-2 items-center">
                    <label class="col-span-2 font-semibold">Секция</label>
                    <div class="col-span-10">
                        <Select
                            :options="availableSections"
                            optionLabel="name"
                            optionValue="id"
                            v-model="form.section_id"
                            placeholder="Выберите секцию"
                            :disabled="!form.building_id"
                            :invalid="hasError('section_id')"
                            fluid
                        />
                        <Message v-if="hasError('section_id')" severity="error" size="small" variant="simple"
                                 class="mt-1">
                            {{ getError('section_id') }}
                        </Message>
                    </div>
                </div>
            </div>
        </div>

        <Fieldset legend="Основные">
            <div class="grid grid-cols-12 1gap-1">

                <div class="col-span-2">
                    <div class="grid grid-cols-12 gap-3 items-center">
                        <label class="col-span-4 font-semibold">Номер</label>
                        <div class="col-span-6">
                            <InputText v-model="form.number" class="w-full" :invalid="hasError('number')"/>
                            <Message v-if="hasError('number')" severity="error" size="small" variant="simple"
                                     class="mt-1">
                                {{ getError('number') }}
                            </Message>
                        </div>
                    </div>
                </div>

                <div class="col-span-2">
                    <div class="grid grid-cols-12 gap-2 items-center">
                        <label class="col-span-4 font-semibold">Этаж</label>
                        <div class="col-span-6">
                            <InputText v-model="form.floor" class="w-full" :invalid="hasError('floor')"/>
                            <Message v-if="hasError('floor')" severity="error" size="small" variant="simple"
                                     class="mt-1">
                                {{ getError('floor') }}
                            </Message>
                        </div>
                    </div>
                </div>


                <div class="col-span-2">
                    <div class="grid grid-cols-12 gap-2 items-center">
                        <label class="col-span-6 font-semibold">Площадь общая</label>
                        <div class="col-span-5">
                            <InputText v-model="form.total_area" class="w-full" :invalid="hasError('total_area')"/>
                        </div>
                    </div>
                    <Message v-if="hasError('total_area')" severity="error" size="small" variant="simple" class="mt-1">
                        {{ getError('total_area') }}
                    </Message>
                </div>

                <div class="col-span-2">
                    <div class="grid grid-cols-12 gap-2 items-center">
                        <label class="col-span-6 font-semibold">Площадь жилая</label>
                        <div class="col-span-5">
                            <InputText v-model="form.living_area" class="w-full" :invalid="hasError('living_area')"/>
                        </div>
                    </div>
                    <Message v-if="hasError('living_area')" severity="error" size="small" variant="simple" class="mt-1">
                        {{ getError('living_area') }}
                    </Message>
                </div>

                <div class="col-span-2">
                    <div class="grid grid-cols-12 gap-2 items-center">
                        <label class="col-span-5 font-semibold">Кол-во комнат</label>
                        <div class="col-span-5">
                            <InputText v-model="form.rooms_count" class="w-full" :invalid="hasError('rooms_count')"/>
                        </div>
                    </div>
                    <Message v-if="hasError('rooms_count')" severity="error" size="small" variant="simple" class="mt-1">
                        {{ getError('rooms_count') }}
                    </Message>
                </div>

                <div class="col-span-2">
                    <div class="grid grid-cols-12 gap-2 items-center">
                        <label class="col-span-8 font-semibold">Кол-во прописанных</label>
                        <div class="col-span-4">
                            <InputText v-model="form.registered_residents" class="w-full"
                                       :invalid="hasError('registered_residents')"/>
                        </div>
                    </div>
                    <Message v-if="hasError('registered_residents')" severity="error" size="small" variant="simple"
                             class="mt-1">
                        {{ getError('registered_residents') }}
                    </Message>
                </div>

            </div>

        </Fieldset>

        <div class="grid grid-cols-12 gap-4 mt-4 mb-4">
            <div class="col-span-6">
                <div class="grid grid-cols-12 gap-2 items-center">
                    <label class="col-span-2 font-semibold">Статус</label>
                    <div class="col-span-9">
                        <Select
                            :options="statusOptions"
                            optionLabel="label"
                            optionValue="value"
                            v-model="form.status"
                            placeholder="Выберите"
                            :invalid="hasError('status')"
                            fluid
                        />
                        <Message v-if="hasError('status')" severity="error" size="small" variant="simple" class="mt-1">
                            {{ getError('status') }}
                        </Message>
                    </div>
                </div>
            </div>
            <div class="col-span-6">
                <div class="grid grid-cols-12 gap-2 items-center">
                    <label class="col-span-3 font-semibold">Тип жилья</label>
                    <div class="col-span-9">
                        <Select
                            :options="typeOptions"
                            optionLabel="label"
                            optionValue="value"
                            v-model="form.type"
                            placeholder="Выберите"
                            :invalid="hasError('type')"
                            fluid
                        />
                        <Message v-if="hasError('type')" severity="error" size="small" variant="simple" class="mt-1">
                            {{ getError('type') }}
                        </Message>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-12 gap-10">
            <div class="col-span-5">
                <Divider align="left">
                    <span class="text-primary font-semibold">Доп. услуги</span>
                </Divider>
                <div v-for="service of extraServices.data" :key="service.id" class="flex items-center gap-2 mb-1">
                    <Checkbox
                        v-model="form.extraServices"
                        :inputId="`service-${service.id}`"
                        :value="service.id"
                    />
                    <label :for="`service-${service.id}`">{{ service.name }}</label>
                </div>
            </div>

            <div class="col-span-7">

                <div class="grid grid-cols-12 gap-2 mb-4">
                    <label class="col-span-1 font-semibold self-center"></label>
                    <div class="col-span-11">
                        <Textarea v-model="form.description" placeholder="Заметки" class="w-full" rows="3"
                                  :invalid="hasError('description')"/>
                        <Message v-if="hasError('description')" severity="error" size="small" variant="simple"
                                 class="mt-1">
                            {{ getError('description') }}
                        </Message>
                    </div>
                </div>
            </div>
        </div>

        <Divider align="left">
            <span class="text-primary font-semibold">Владельцы</span>
        </Divider>

        <!-- Добавление владельца -->
        <div class="flex gap-2 items-center">
            <AutoComplete
                v-model="selectedOwner"
                :suggestions="searchResults"
                field="id"
                placeholder="ФИО, телефон"
                option-label="name"
                @complete="searchOwners"
                class="w-62"
            >
            </AutoComplete>

            <Button
                label="Выбрать"
                icon="pi pi-check"
                :disabled="!selectedOwner"
                @click="addOwnerFromSelect"
            />

            <ToggleButton v-model="showNewForm" onLabel="Закрыть" offIcon="pi pi-plus" offLabel="Добавить"/>

            <Message v-if="hasError('owners')" severity="error" size="small" variant="simple" class="mt-1">
                {{ getError('owners') }}
            </Message>

        </div>

        <div v-if="showNewForm" class="flex gap-2 items-center mt-5">
            <InputText v-model="newOwner.name" placeholder="ФИО" class="w-fill"/>
            <InputMask
                v-model="newOwner.phone"
                class="w-fill"
                mask="(999) 99 999-9999"
                placeholder="(380) 12 345-6789"

            />
            <InputText v-model="newOwner.email" placeholder="Email" class="w-fill"/>
            <Button label="Добавить нового" icon="pi pi-plus" @click="addNewOwner"/>
        </div>

        <DataTable v-if="form.owners.length" :value="form.owners" class="w-full mt-2">
            <Column header="Плательщик" style="width: 10%;">
                <template #body="{ data }">
                    <Checkbox
                        v-model="data.is_payer"
                        binary
                    />
                </template>
            </Column>
            <Column header="Процент оплаты" style="width: 10%;">
                <template #body="{ data }">
                    <InputText
                        v-model="data.payment_percent"
                        class="w-full"

                    />
                    <Message
                        v-if="hasError(`owners.${index}.payment_percent`)"
                        severity="error"
                        size="small"
                        variant="simple"
                        class="mt-1"
                    >
                        {{ getError(`owners.${index}.payment_percent`) }}
                    </Message>
                </template>
            </Column>
            <Column field="name" header="ФИО" style="width: 40%">
                <template #body="{ data, index }">
                    {{ data.name }}
                    <Message
                        v-if="hasError(`owners.${index}.name`)"
                        severity="error"
                        size="small"
                        variant="simple"
                        class="mt-1"
                    >
                        {{ getError(`owners.${index}.name`) }}
                    </Message>
                </template>
            </Column>

            <Column field="email" header="Email" style="width: 15%">
                <template #body="{ data, index }">
                    {{ data.email }}
                    <Message
                        v-if="hasError(`owners.${index}.email`)"
                        severity="error"
                        size="small"
                        variant="simple"
                        class="mt-1"
                    >
                        {{ getError(`owners.${index}.email`) }}
                    </Message>
                </template>
            </Column>

            <Column field="phone" header="Телефон" style="width: 25%">
                <template #body="{ data, index }">
                    {{ data.phone }}
                    <Message
                        v-if="hasError(`owners.${index}.phone`)"
                        severity="error"
                        size="small"
                        variant="simple"
                        class="mt-1"
                    >
                        {{ getError(`owners.${index}.phone`) }}
                    </Message>
                </template>
            </Column>

            <Column header="">
                <template #body="{ data }">
                    <Button icon="pi pi-trash" severity="danger" text @click="removeOwner(data.id)"/>
                </template>
            </Column>
        </DataTable>

        <Empty v-else/>


        <div class="flex justify-end gap-2 mt-6">
            <Button type="button" label="Отменить" severity="secondary" @click="closeModal"/>
            <Button type="button" label="Сохранить" :loading="form.processing" @click="submit"/>
        </div>
    </Dialog>
</template>
