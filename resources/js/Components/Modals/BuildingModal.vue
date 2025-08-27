<script setup>
import {ref, watch} from 'vue';
import {useForm} from '@inertiajs/vue3';
import {useToast} from 'primevue/usetoast';
import {v4 as uuid} from 'uuid';
import Empty from "@/Pages/Empty.vue";

const props = defineProps({
    show: Boolean,
    building: {
        type: Object,
        default: null
    },
    basicServices: {
        type: Object,
        default: null
    }
});

const emit = defineEmits(['update:show', 'refresh']);
const toast = useToast();
const displayModal = ref(false);
const sections = ref([]);
const newSection = ref({name: '', floors_count: ''});
const editingRows = ref([]);

const sectionErrors = ref({
    name: null,
    floors_count: null
});

const form = useForm({
    id: null,
    name: '',
    address: '',
    description: '',
    sections: [],
    services: [],
});

watch(() => props.show, (value) => {
    displayModal.value = value;
    if (value) {
        if (props.building) {
            form.id = props.building.id;
            form.name = props.building.name;
            form.address = props.building.address;
            form.description = props.building.description;
            sections.value = props.building.sections || [];
            form.services = props.building.services.map(s => s.id);
        } else {
            form.reset();
            sections.value = [];
        }
    }
});

const validateAndAddSection = () => {
    sectionErrors.value = {
        name: null,
        floors_count: null
    };

    if (!newSection.value.name.trim()) {
        sectionErrors.value.name = 'Введите название секции';
        return;
    }

    const floorsInput = newSection.value.floors_count;
    const floorsNum = Number(floorsInput);

    if (!floorsInput) {
        sectionErrors.value.floors_count = 'Введите количество этажей';
        return;
    }

    if (isNaN(floorsNum)) {
        sectionErrors.value.floors_count = 'Этаж должен быть числом';
        return;
    }

    if (!Number.isInteger(floorsNum)) {
        sectionErrors.value.floors_count = 'Введите целое число для этажа';
        return;
    }

    if (floorsNum < 1) {
        sectionErrors.value.floors_count = 'Минимум 1 этаж';
        return;
    }

    if (floorsNum > 50) {
        sectionErrors.value.floors_count = 'Максимум 50 этажей';
        return;
    }

    addSection();
};

const addSection = () => {
    if (!newSection.value.name) return;

    sections.value.push({
        id: uuid(),
        name: newSection.value.name,
        floors_count: newSection.value.floors_count,
        isNew: true
    });

    newSection.value = {name: '', floors_count: ''};
};

const removeSection = (index) => {
    sections.value.splice(index, 1);
};

const onRowEditSave = (event) => {
    let {newData, index} = event;

    form.clearErrors()
    sections.value[index] = newData;
};

const submit = () => {
    form.sections = sections.value;

    const url = form.id
        ? route('dashboard.buildings.update', form.id)
        : route('dashboard.buildings.store');

    const method = form.id ? 'put' : 'post';

    form.transform(data => ({
        ...data,
        sections: data.sections.map(section => ({
            name: section.name,
            floors_count: section.floors_count
        }))
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
            :header="form.id ? 'Редактирование' : 'Добавление'"
            :style="{ width: '50rem' }"
            @hide="closeModal"
    >
        <span
            class="text-surface-500 dark:text-surface-400 block mb-6">Управление информацией о здании и его секциями.</span>

        <!-- Название -->
        <div class="grid grid-cols-12 gap-2 mb-4">
            <label class="col-span-2 font-semibold self-center">Название</label>
            <div class="col-span-10">
                <InputText v-model="form.name" class="w-full" :invalid="hasError('name')"/>
                <Message v-if="hasError('name')" severity="error" size="small" variant="simple" class="mt-1">
                    {{ getError('name') }}
                </Message>
            </div>
        </div>

        <!-- Адрес -->
        <div class="grid grid-cols-12 gap-2 mb-4">
            <label class="col-span-2 font-semibold self-center">Адрес</label>
            <div class="col-span-10">
                <InputText v-model="form.address" class="w-full" :invalid="hasError('address')"/>
                <Message v-if="hasError('address')" severity="error" size="small" variant="simple" class="mt-1">
                    {{ getError('address') }}
                </Message>
            </div>
        </div>

        <!-- Описание -->
        <div class="grid grid-cols-12 gap-2 mb-4">
            <label class="col-span-2 font-semibold self-center">Описание</label>
            <div class="col-span-10">
                <Textarea v-model="form.description" class="w-full" rows="3" :invalid="hasError('description')"/>
                <Message v-if="hasError('description')" severity="error" size="small" variant="simple" class="mt-1">
                    {{ getError('description') }}
                </Message>
            </div>
        </div>

        <div class="grid grid-cols-12 gap-10">
            <!-- Левая колонка: Базовые услуги -->
            <div class="col-span-4">

                <Divider align="left">
                    <span class="text-primary font-semibold">Базовые услуги</span>
                </Divider>

                <div v-for="service of basicServices.data" :key="service.id" class="flex items-center gap-2 mb-1">
                    <Checkbox
                        v-model="form.services"
                        :inputId="`service-${service.id}`"
                        :value="service.id"
                    />
                    <label :for="`service-${service.id}`">{{ service.name }}</label>
                </div>

                <Message v-if="hasError('services')" severity="error" size="small" variant="simple" class="mt-1">
                    {{ getError('services') }}
                </Message>

                <Empty v-if="!basicServices.data.length" />
            </div>

            <div class="col-span-8">
                <Divider align="left">
                    <span class="text-primary font-semibold">Секции/парадные</span>
                </Divider>

                <!-- Добавление секции -->
                <div class="grid grid-cols-12 gap-2 items-center mb-4">
                    <div class="col-span-7">
                        <InputText v-model="newSection.name" placeholder="Название" class="w-full"
                                   @keyup.enter="validateAndAddSection"/>
                    </div>
                    <div class="col-span-3">
                        <InputText v-model="newSection.floors_count" placeholder="Этажей" class="w-full"
                                   @keyup.enter="validateAndAddSection"/>
                    </div>
                    <div class="col-span-2">
                        <Button icon="pi pi-plus" class="p-button-rounded p-button-outlined"
                                :disabled="!newSection.name"
                                @click="validateAndAddSection"/>
                    </div>
                </div>
                <div class="col-span-12">
                    <Message v-if="sectionErrors.floors_count" severity="error" size="small" variant="simple"
                             class="mt-1">
                        {{ sectionErrors.floors_count }}
                    </Message>
                    <Message v-if="sectionErrors.name" severity="error" size="small" variant="simple" class="mt-1">
                        {{ sectionErrors.name }}
                    </Message>
                </div>


                <!-- Таблица секций -->
                <DataTable v-if="sections.length"
                           :value="sections"
                           v-model:editingRows="editingRows"
                           class="mb-4"
                           :rows="5"
                           :paginator="sections.length > 5"
                           editMode="row"
                           dataKey="id"
                           @row-edit-save="onRowEditSave">

                    <Column field="name" header="Название" style="width: 60%">
                        <template #editor="{ data, field, index }">
                            <div>
                                <InputText v-model="data[field]" fluid
                                           :invalid="hasError(`sections.${index}.${field}`)"/>
                                <small v-if="hasError(`sections.${index}.${field}`)" class="p-error">
                                    {{ getError(`sections.${index}.${field}`) }}
                                </small>
                            </div>
                        </template>

                        <template #body="{ data, index }">
                            {{ data.name }}
                            <Message
                                v-if="hasError(`sections.${index}.name`)"
                                severity="error"
                                size="small"
                                variant="simple"
                                class="mt-1"
                            >
                                {{ getError(`sections.${index}.name`) }}
                            </Message>
                        </template>
                    </Column>

                    <Column field="floors_count" header="Этажность" style="width: 20%" bodyStyle="text-align:center">
                        <template #editor="{ data, field, index }">
                            <div>
                                <InputNumber v-model="data[field]" :min="1" :max="50"
                                             :invalid="hasError(`sections.${index}.${field}`)"/>
                                <small v-if="hasError(`sections.${index}.${field}`)" class="p-error">
                                    {{ getError(`sections.${index}.${field}`) }}
                                </small>

                            </div>
                        </template>

                        <template #body="{ data, index }">
                            {{ data.floors_count }}
                            <Message
                                v-if="hasError(`sections.${index}.floors_count`)"
                                severity="error"
                                size="small"
                                variant="simple"
                                class="mt-1"
                            >
                                {{ getError(`sections.${index}.floors_count`) }}
                            </Message>
                        </template>
                    </Column>

                    <Column :rowEditor="true" style="width: 10%; min-width: 8rem"
                            bodyStyle="text-align:center"></Column>

                    <!--                <Column header="" style="width: 1%;">-->
                    <!--                    <template #body="{ index }">-->
                    <!--                        <Button icon="pi pi-trash" class="p-button-rounded p-button-text p-button-danger" @click="removeSection(index)" />-->
                    <!--                    </template>-->
                    <!--                </Column>-->

                </DataTable>

                <template v-else>
                    <Message v-if="hasError('sections')" severity="error" size="small" variant="simple" class="mb-4">
                        {{ getError('sections') }}
                    </Message>
                </template>
            </div>
        </div>

        <div class="flex justify-end gap-2 mt-6">
            <Button type="button" label="Отменить" severity="secondary" @click="closeModal"/>
            <Button type="button" label="Сохранить" :disabled="!form.isDirty" :loading="form.processing"
                    @click="submit"/>
        </div>
    </Dialog>
</template>
