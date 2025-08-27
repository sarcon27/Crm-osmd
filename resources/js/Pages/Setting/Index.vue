<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import {Head, router} from '@inertiajs/vue3';
import {ref, computed, onMounted} from 'vue';
import {useToast} from 'primevue/usetoast';

const props = defineProps({
    title: String,
    settings: Object,
});

const editingRows = ref([]);
const localSettings = ref({});
const toast = useToast();
let idCounter = 0;

onMounted(() => {
    if (props.settings && typeof props.settings === 'object') {
        localSettings.value = {...props.settings};
    }
});

const settingsArray = computed(() => {
    if (!localSettings.value || typeof localSettings.value !== 'object') {
        return [];
    }

    return Object.entries(localSettings.value).map(([key, value]) => ({
        id: idCounter++,
        key,
        value: value === null ? '' : (typeof value === 'object' ? JSON.stringify(value) : String(value)),
        originalValue: value
    }));

});

const onRowEditSave = (event) => {
    const {newData} = event;

    try {
        const parsedValue = newData.value;

        router.put(route('dashboard.settings.save'), {
            key: newData.key,
            value: parsedValue
        }, {
            preserveScroll: true,
            onSuccess: () => {
                localSettings.value[newData.key] = parsedValue
                toast.add({
                    severity: 'success',
                    summary: 'Успешно',
                    life: 3000
                });
            },
            onError: (errors) => {
                toast.add({
                    severity: 'error',
                    summary: 'Error',
                    life: 3000
                });

            }
        });
    } catch (error) {
        console.error('Ошибка обработки значения:', error);
    }
};

</script>

<template>
    <app-layout>
        <Head :title="title"/>

        <div class="card">
            <Toolbar class="mb-6">
                <template #start>
                    <div class="font-semibold text-xl">Настройки</div>
                </template>
            </Toolbar>

            <DataTable
                v-model:editingRows="editingRows"
                :value="settingsArray"
                editMode="row"
                dataKey="id"
                responsiveLayout="scroll"
                @row-edit-save="onRowEditSave"

            >
                <Column field="key" header="Ключ" :sortable="true">
                    <template #body="{ data }">
                        <span class="font-mono text-sm bg-blue-100 px-2 py-1 rounded">
                            {{ data.key }}
                        </span>
                    </template>
                </Column>

                <Column field="value" header="Значение" style="width: 40%">
                    <template #body="{ data }">
                        <div class="truncate max-w-md text-sm">
                            {{ data.value }}
                        </div>
                    </template>
                    <template #editor="{ data }">
                        <InputText
                            v-model="data.value"
                            rows="2"
                            class="w-full text-sm"
                            :autoResize="true"
                            placeholder="Введите значение..."
                        />
                    </template>
                </Column>

                <Column
                    header="Тип"
                    style="width: 100px"
                >
                    <template #body="{ data }">
                        <Tag
                            :value="typeof localSettings[data.key]"
                            :severity="typeof localSettings[data.key] === 'boolean' ? 'success' :
                                      typeof localSettings[data.key] === 'number' ? 'warning' :
                                      typeof localSettings[data.key] === 'string' ? 'info' : 'secondary'"
                            class="text-xs"
                        />
                    </template>
                </Column>

                <Column :rowEditor="true" style="width: 10%; min-width: 8rem" bodyStyle="text-align:center"/>

            </DataTable>

        </div>

    </app-layout>
</template>

<style scoped>
.truncate {
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.font-mono {
    font-family: 'Monaco', 'Menlo', 'Ubuntu Mono', monospace;
}
</style>
