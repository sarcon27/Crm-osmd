<script setup>
import {useResourceForm} from '@/composables/useResourceFormSingle';

const props = defineProps({
    show: Boolean,
    measure: Object
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
    resourceName: 'measures',
    propKey: 'measure',

    defaultFormData: {
        name: '',
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

        <div class="flex justify-end gap-2 mt-6">
            <Button type="button" label="Отменить" severity="secondary" @click="closeModal"/>
            <Button type="button" label="Сохранить" :disabled="!form.isDirty" :loading="form.processing"
                    @click="submit"/>
        </div>
    </Dialog>

</template>
