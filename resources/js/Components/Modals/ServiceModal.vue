<script setup>
import {useResourceForm} from '@/composables/useResourceFormSingle';

const props = defineProps({
    show: Boolean,
    service: Object,

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
    resourceName: 'services',
    propKey: 'service',
    defaultFormData: {
        name: '',
        notes: '',
        is_base: false,
        is_auto: false,
    },
})

</script>

<template>
    <Dialog v-model:visible="displayModal"
            modal
            :header="form.id ? 'Редактирование' : 'Добавление'"
            :style="{ width: '50rem' }"
            @hide="closeModal">

        <div class="grid grid-cols-12 gap-2 mb-4">
            <label class="col-span-2 font-semibold self-center">Название</label>
            <div class="col-span-10">
                <InputText v-model="form.name" class="w-full" :invalid="hasError('name')"/>
                <Message v-if="hasError('name')" severity="error" size="small" variant="simple" class="mt-1">
                    {{ getError('name') }}
                </Message>
            </div>
        </div>

        <Divider/>

        <div class="grid grid-cols-12 gap-4 mb-4">

            <div class="col-span-3">
                <div class="grid grid-cols-12 gap-3">
                    <label class="col-span-8 w-full font-semibold self-center">Базовый</label>
                    <div class="col-span-4">
                        <Checkbox
                            v-model="form.is_base"
                            binary
                            :invalid="hasError('is_base')"
                        />
                        <Message v-if="hasError('is_base')" severity="error" size="small" variant="simple"
                                 class="mt-1">
                            {{ getError('is_base') }}
                        </Message>
                    </div>
                </div>
            </div>

            <div class="col-span-3">
                <div class="grid grid-cols-12 gap-2">
                    <label class="col-span-7 w-full font-semibold self-center">Автоплатеж</label>
                    <div class="col-span-5">
                        <Checkbox
                            v-model="form.is_auto"
                            binary
                            :invalid="hasError('is_auto')"
                        />

                        <Message v-if="hasError('is_auto')" severity="error" size="small" variant="simple" class="mt-1">
                            {{ getError('is_auto') }}
                        </Message>
                    </div>
                </div>
            </div>
        </div>

        <Divider/>



        <div class="grid grid-cols-12 gap-2 mb-4">
            <label class="col-span-2 font-semibold self-center">Примечания</label>
            <div class="col-span-10">
                <Textarea v-model="form.notes" class="w-full" rows="3" :invalid="hasError('notes')"/>
                <Message v-if="hasError('notes')" severity="error" size="small" variant="simple" class="mt-1">
                    {{ getError('note') }}
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
