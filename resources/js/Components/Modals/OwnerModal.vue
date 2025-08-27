<script setup>
import {useResourceForm} from '@/composables/useResourceFormSingle';
import EmptyPage from '@/Pages/Empty.vue'

const props = defineProps({
    show: Boolean,
    owner: Object
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
    resourceName: 'owners',
    propKey: 'owner',
    defaultFormData: {
        name: '',
        phone: '',
        email: '',
        notes: ''
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
            <label class="col-span-2 font-semibold self-center">ФИО</label>
            <div class="col-span-10">
                <InputText v-model="form.name" class="w-full" :invalid="hasError('name')"/>
                <Message v-if="hasError('name')" severity="error" size="small" variant="simple" class="mt-1">
                    {{ getError('name') }}
                </Message>
            </div>
        </div>

        <div class="grid grid-cols-12 gap-4 mb-4">
            <div class="col-span-6">
                <div class="grid grid-cols-12 gap-2">
                    <label class="col-span-4 font-semibold self-center">Телефон</label>
                    <div class="col-span-8">
                        <InputMask
                            v-model="form.phone"
                            class="w-full"
                            :invalid="hasError('phone')"
                            mask="(999) 99 999-9999"
                            placeholder="(380) 12 345-6789"
                        />
                        <Message v-if="hasError('phone')" severity="error" size="small" variant="simple" class="mt-1">
                            {{ getError('phone') }}
                        </Message>
                    </div>
                </div>
            </div>

            <div class="col-span-6">
                <div class="grid grid-cols-12 gap-2">
                    <label class="col-span-2 font-semibold self-center">Email</label>
                    <div class="col-span-10">
                        <InputText v-model="form.email" class="w-full" :invalid="hasError('email')"/>
                        <Message v-if="hasError('email')" severity="error" size="small" variant="simple" class="mt-1">
                            {{ getError('email') }}
                        </Message>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-12 gap-2 mb-4">
            <label class="col-span-2 font-semibold self-center">Заметки</label>
            <div class="col-span-10">
                <Textarea v-model="form.notes" class="w-full" :invalid="hasError('notes')"/>
                <Message v-if="hasError('notes')" severity="error" size="small" variant="simple" class="mt-1">
                    {{ getError('notes') }}
                </Message>
            </div>
        </div>

        <Divider align="left">
            <span class="text-primary font-semibold">Квартиры</span>
        </Divider>

        <div class="grid grid-cols-12 gap-2 mb-4">
            <div class="col-span-12">

                <DataTable  v-if="owner?.apartments?.data?.length" :value="owner?.apartments?.data ?? []">
                    <Column header="Дом">
                        <template #body="{ data }">
                            {{ data.building?.name }}
                        </template>
                    </Column>

                    <Column header="Адрес">
                        <template #body="{ data }">
                            {{ data.building?.address }}
                        </template>
                    </Column>

                    <Column header="Секция">
                        <template #body="{ data }">
                            {{ data.section?.name }}
                        </template>
                    </Column>

                    <Column field="number" header="Квартира"/>

                    <Column header="Плательщик" bodyStyle="text-align:center">
                        <template #body="{ data }">
                            <i v-if="data.is_payer" class="pi pi-check text-green-500"></i>
                            <i v-else class="pi pi-times text-red-500"></i>
                        </template>
                    </Column>

                </DataTable>

                <EmptyPage v-else/>
            </div>

        </div>

        <div class="flex justify-end gap-2 mt-6">
            <Button type="button" label="Отменить" severity="secondary" @click="closeModal"/>
            <Button type="button" label="Сохранить" :disabled="!form.isDirty" :loading="form.processing"
                    @click="submit"/>
        </div>
    </Dialog>

</template>
