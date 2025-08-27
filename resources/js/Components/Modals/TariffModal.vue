<script setup>
import {useResourceForm} from '@/composables/useResourceFormSingle';

const props = defineProps({
    show: Boolean,
    tariff: Object,
    measures: Object,
    services: Object,
    statusOptions: Array,
    typeOptions: Array
});

const emit = defineEmits(['update:show', 'refresh']);

const {
    displayModal,
    closeModal,
    form,
    hasError,
    getError,
    submit
} = useResourceForm(props, emit, {
    resourceName: 'tariffs',
    propKey: 'tariff',
    defaultFormData: {
        measure_id: '',
        service_id: '',
        price: '',
        status: '',
        type: '',
        notes: '',
        start_date: '',
        end_date:''

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

</script>

<template>
    <Dialog v-model:visible="displayModal"
            modal
            :header="form.id ? 'Редактирование' : 'Добавление'"
            :style="{ width: '50rem' }"
            @hide="closeModal">

        <div class="grid grid-cols-12 gap-2 mb-4">
            <label class="col-span-2 font-semibold self-center">Услуга</label>
            <div class="col-span-10">
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
            <div class="col-span-4">
                <div class="grid grid-cols-12 gap-2">
                    <label class="col-span-6 font-semibold self-center">Стоимость, грн</label>
                    <div class="col-span-6">
                        <InputText v-model="form.price" class="w-full" :invalid="hasError('price')"  placeholder="0.00"/>
                        <Message v-if="hasError('price')" severity="error" size="small" variant="simple" class="mt-1">
                            {{ getError('price') }}
                        </Message>
                    </div>
                </div>
            </div>

            <div class="col-span-3">
                <div class="grid grid-cols-12 gap-2">
                    <label class="col-span-2 font-semibold self-center">за</label>
                    <div class="col-span-10">
                        <Select
                            :options="measures.data"
                            optionLabel="name"
                            optionValue="id"
                            v-model="form.measure_id"
                            placeholder="Единица измерения"
                            fluid
                            :invalid="hasError('measure_id')"
                        />
                        <Message v-if="hasError('measure_id')" severity="error" size="small" variant="simple"
                                 class="mt-1">
                            {{ getError('measure_id') }}
                        </Message>
                    </div>
                </div>
            </div>

        </div>


        <div class="grid grid-cols-12 gap-4 mt-4 mb-4">
            <div class="col-span-6">
                <div class="grid grid-cols-12 gap-2 items-center">
                    <label class="col-span-4 font-semibold">Статус</label>
                    <div class="col-span-8">
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
                    <label class="col-span-2 font-semibold">Тип</label>
                    <div class="col-span-10">
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

        <div class="grid grid-cols-12 gap-4 mt-4 mb-4">

            <div class="col-span-6">
                <div class="grid grid-cols-12 gap-2 items-center">
                    <label class="col-span-4 font-semibold">Дата начала</label>
                    <div class="col-span-8">
                        <DatePicker v-model="form.start_date"
                                    showWeek
                                    dateFormat="dd-mm-yy"
                                    showIcon fluid iconDisplay="input"
                                    :invalid="hasError('start_date')"
                                    placeholder="Выберите"
                        />
                        <Message v-if="hasError('start_date')" severity="error" size="small" variant="simple" class="mt-1">
                            {{ getError('start_date') }}
                        </Message>
                    </div>
                </div>
            </div>

            <div class="col-span-6">
                <div class="grid grid-cols-12 gap-2 items-center">
                    <label class="col-span-4 font-semibold">Действует до</label>
                    <div class="col-span-8">
                        <DatePicker v-model="form.end_date"
                                    showWeek
                                    dateFormat="dd-mm-yy"
                                    showIcon fluid iconDisplay="input"
                                    :invalid="hasError('end_date')"
                                    placeholder="Выберите"

                                    />
                        <Message v-if="hasError('end_date')" severity="error" size="small" variant="simple" class="mt-1">
                            {{ getError('end_date') }}
                        </Message>
                    </div>
                </div>
            </div>
        </div>

        <Divider></Divider>

        <div class="grid grid-cols-12 gap-2 mb-4">
            <label class="col-span-2 font-semibold self-center">Заметки</label>
            <div class="col-span-10">
                <Textarea v-model="form.notes" class="w-full" rows="3" :invalid="hasError('notes')"/>
                <Message v-if="hasError('notes')" severity="error" size="small" variant="simple" class="mt-1">
                    {{ getError('notes') }}
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
