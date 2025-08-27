<script setup>
import {ref, watch} from 'vue';
import {router, useForm} from "@inertiajs/vue3";
import {useToast} from 'primevue/usetoast';

const props = defineProps({
    show: Boolean,
    invoice: {
        type: Object,
        default: null
    }
});

const emit = defineEmits(['update:show', 'refresh']);
const displayModal = ref(false);

watch(() => props.show, async (isOpen) => {
    displayModal.value = isOpen;
});

const closeModal = () => {
    displayModal.value = false;
    emit('update:show', false);
};


const formatPeriod = (period) => {
    const [year, month] = period.split('-');
    const months = ['Январь', 'Февраль', 'Март', 'Апрель', 'Май', 'Июнь', 'Июль', 'Август', 'Сентябрь', 'Октябрь', 'Ноябрь', 'Декабрь'];
    return `${months[parseInt(month) - 1]} ${year}`;
};

const formatDateShort = (dateString) => {
    return new Date(dateString).toLocaleDateString('ru-RU', {
        day: '2-digit',
        month: '2-digit'
    });
};

const formatDateTime = (dateTimeString) => {
    return new Date(dateTimeString).toLocaleString('ru-RU', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
};

const formatPhone = (phone) => {
    return phone.replace(/(\d{3}) (\d{2}) (\d{3})-(\d{4})/, '+38 ($1) $2-$3-$4');
};

const getStatusClass = (status) => {
    return status === 'opened' ? 'status-open' : 'status-closed';
};

const getStatusText = (status) => {
    return status === 'opened' ? 'Ожидает оплаты' : 'Оплачен';
};

</script>

<template>
    <Dialog
        v-model:visible="displayModal"
        modal
        header="Просмотр документа"
        :style="{ width: '85rem' }"
        @hide="closeModal">


        <div class="invoice-container">
            <!-- Шапка счета -->
            <div class="invoice-header">
                <div class="header-left">
                    <h1 class="invoice-title">Счет на оплату #{{ invoice.id }}</h1>
                    <p class="invoice-period">Период: {{ formatPeriod(invoice.period) }}</p>
                </div>
                <div class="header-right">
                    <div class="status-badge" :class="getStatusClass(invoice.transactionEntry.transaction.status)">
                        {{ getStatusText(invoice.transactionEntry.transaction.status) }}
                    </div>
                    <div class="total-amount">
                        <span class="amount-label">К оплате:</span>
                        <span class="amount-value">{{ invoice.total }} ₴</span>
                    </div>
                </div>
            </div>

            <Divider />

            <!-- Информационные блоки -->
            <div class="info-grid">
                <!-- Квартира -->
                <div class="info-card">
                    <div class="card-header">
                        <i class="pi pi-home"></i>
                        <h3>Квартира</h3>
                    </div>
                    <div class="card-content">
                        <div class="info-item">
                            <span class="label">Адрес:</span>
                            <span class="value">{{ invoice.apartment.building.name }}</span>
                        </div>
                        <div class="info-item">
                            <span class="label">Квартира:</span>
                            <span class="value">№{{ invoice.apartment.number }}, этаж {{ invoice.apartment.floor }}</span>
                        </div>
                        <div class="info-item">
                            <span class="label">Площадь:</span>
                            <span class="value">{{ invoice.apartment.total_area }} м²</span>
                        </div>
                    </div>
                </div>

                <!-- Собственник -->
                <div class="info-card">
                    <div class="card-header">
                        <i class="pi pi-user"></i>
                        <h3>Собственник</h3>
                    </div>
                    <div class="card-content">
                        <div class="info-item">
                            <span class="label">ФИО:</span>
                            <span class="value">{{ invoice.owner.name }}</span>
                        </div>
                        <div class="info-item">
                            <span class="label">Телефон:</span>
                            <span class="value">{{ formatPhone(invoice.owner.phone) }}</span>
                        </div>
                        <div class="info-item">
                            <span class="label">Проживает:</span>
                            <span class="value">{{ invoice.apartment.registered_residents }} чел.</span>
                        </div>
                    </div>
                </div>

                <!-- Финансы -->
                <div class="info-card">
                    <div class="card-header">
                        <i class="pi pi-wallet"></i>
                        <h3>Финансы</h3>
                    </div>
                    <div class="card-content">
                        <div class="info-item">
                            <span class="label">Лицевой счет:</span>
                            <span class="value">№{{ invoice.apartment.financeAccount.number }}</span>
                        </div>
                        <div class="info-item">
                            <span class="label">Баланс:</span>
                            <span class="value" :class="{'positive': invoice.apartment.financeAccount.balance > 0, 'negative': invoice.apartment.financeAccount.balance < 0}">
              {{ invoice.apartment.financeAccount.balance }} ₴
            </span>
                        </div>
                        <div class="info-item">
                            <span class="label">Задолженность:</span>
                            <span class="value" :class="{'debt': invoice.debt > 0}">
              {{ invoice.debt }} ₴
            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Таблица услуг -->
            <div class="services-section">
                <div class="section-header">
                    <h2><i class="pi pi-list"></i> Детализация услуг</h2>
                </div>

                <DataTable :value="invoice.items" class="services-table" responsiveLayout="scroll">
                    <Column header="Услуга">
                        <template #body="slotProps">
                            <div class="service-info">
                                <div class="service-name">Услуга #{{ slotProps.data.tariff.id }}</div>
                                <div class="service-measure">{{ slotProps.data.measure.name }}</div>
                            </div>
                        </template>
                    </Column>
                    <Column header="Количество" style="width: 120px">
                        <template #body="slotProps">
                            <div class="quantity-cell">
                                <span class="quantity-value">{{ slotProps.data.quantity }}</span>
                                <span class="measure-unit">{{ slotProps.data.measure.name }}</span>
                            </div>
                        </template>
                    </Column>
                    <Column header="Сумма" style="width: 120px">
                        <template #body="slotProps">
                            <div class="amount-cell" :class="{'zero-amount': slotProps.data.amount === '0.00'}">
                                {{ slotProps.data.amount }} ₴
                            </div>
                        </template>
                    </Column>
                    <Column header="Дата" style="width: 100px">
                        <template #body="slotProps">
                            <div class="date-cell">
                                {{ formatDateShort(slotProps.data.created_at) }}
                            </div>
                        </template>
                    </Column>
                </DataTable>

                <!-- Итоговая сумма -->
                <div class="total-section">
                    <div class="total-line">
                        <span class="total-label">Общая сумма:</span>
                        <span class="total-value">{{ invoice.total }} ₴</span>
                    </div>
                </div>
            </div>

            <!-- Информация о транзакции -->
            <div class="transaction-section" v-if="invoice.transactionEntry">
                <div class="section-header">
                    <h2><i class="pi pi-credit-card"></i> Информация о платеже</h2>
                </div>
                <div class="transaction-info">
                    <div class="transaction-item">
                        <span class="label">Транзакция:</span>
                        <span class="value">#{{ invoice.transactionEntry.transaction.id }}</span>
                    </div>
                    <div class="transaction-item">
                        <span class="label">Назначение:</span>
                        <span class="value">{{ invoice.transactionEntry.transaction.name }}</span>
                    </div>
                    <div class="transaction-item">
                        <span class="label">Дата проведения:</span>
                        <span class="value">{{ formatDateTime(invoice.transactionEntry.transaction.posted) }}</span>
                    </div>
                </div>
            </div>

            <!-- Кнопки действий -->
            <div class="actions-section">
                <Button label="Распечатать счет" icon="pi pi-print" class="p-button-outlined" />
                <Button label="Оплатить онлайн" icon="pi pi-credit-card" class="p-button-success" />
                <Button label="Скачать PDF" icon="pi pi-download" class="p-button-help" />
                <Button label="Оспорить" icon="pi pi-question-circle" class="p-button-secondary" />
            </div>

            <!-- Футер -->
            <Divider />
            <div class="invoice-footer">
                <p class="footer-text">Счет создан: {{ formatDateTime(invoice.created_at) }}</p>
<!--                <p class="footer-note">При возникновении вопросов обращайтесь в управляющую компанию</p>-->
            </div>
        </div>


    </Dialog>
</template>

<style scoped>
.invoice-container {
    max-width: 1000px;
    margin: 0 auto;
    padding: 2rem;
    background: white;
    border-rad2ius: 12px;
    box-shadsow: 0 4px 6px rgba(0, 0, 0, 0.05);
}

.invoice-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 1.5rem;
}

.invoice-title {
    font-size: 1.8rem;
    font-weight: 700;
    color: #2c3e50;
    margin: 0 0 0.5rem 0;
}

.invoice-period {
    color: #7f8c8d;
    margin: 0;
    font-size: 1.1rem;
}

.header-right {
    text-align: right;
}

.status-badge {
    display: inline-block;
    padding: 0.5rem 1rem;
    border-radius: 20px;
    font-weight: 600;
    font-size: 0.9rem;
    margin-bottom: 0.5rem;
}

.status-open {
    background: #fff3cd;
    color: #856404;
}

.status-closed {
    background: #d4edda;
    color: #155724;
}

.total-amount {
    margin-top: 0.5rem;
}

.amount-label {
    display: block;
    font-size: 0.9rem;
    color: #7f8c8d;
}

.amount-value {
    font-size: 1.8rem;
    font-weight: 700;
    color: #2c3e50;
}

.info-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 1.5rem;
    margin: 2rem 0;
}

.info-card {
    background: #f8f9fa;
    bordedr-radius: 8px;
    padding: 1.5rem;
    bordder-left: 4px solid #3498db;
}

.card-header {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    margin-bottom: 1rem;
}

.card-header h3 {
    margin: 0;
    font-size: 1.1rem;
    color: #2c3e50;
}

.card-header i {
    color: #3498db;
    font-size: 1.2rem;
}

.info-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.5rem 0;
    border-bottom: 1px solid #e9ecef;
}

.info-item:last-child {
    border-bottom: none;
}

.label {
    font-weight: 500;
    color: #7f8c8d;
}

.value {
    font-weight: 600;
    color: #2c3e50;
}

.positive {
    color: #27ae60 !important;
}

.negative, .debt {
    color: #e74c3c !important;
}

.section-header {
    margin: 2rem 0 1rem 0;
}

.section-header h2 {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 1.3rem;
    color: #2c3e50;
    margin: 0;
}

.services-table {
    margin: 1rem 0;
}

.service-info {
    display: flex;
    flex-direction: column;
}

.service-name {
    font-weight: 600;
    color: #2c3e50;
}

.service-measure {
    font-size: 0.9rem;
    color: #7f8c8d;
}

.quantity-cell {
    display: flex;
    align-items: center;
    gap: 0.25rem;
}

.quantity-value {
    font-weight: 600;
}

.measure-unit {
    font-size: 0.8rem;
    color: #7f8c8d;
}

.amount-cell {
    font-weight: 600;
    color: #27ae60;
    text-align: right;
}

.zero-amount {
    color: #7f8c8d !important;
}

.date-cell {
    color: #7f8c8d;
    font-size: 0.9rem;
}

.total-section {
    background: #f8f9fa;
    padding: 1.5rem;
    border-radius: 8px;
    margin-top: 1rem;
}

.total-line {
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-size: 1.2rem;
}

.total-label {
    font-weight: 600;
    color: #2c3e50;
}

.total-value {
    font-weight: 700;
    color: #2c3e50;
    font-size: 1.4rem;
}

.transaction-info {
    background: #f8f9fa;
    padding: 1.5rem;
    border-radius: 8px;
    margin-top: 1rem;
}

.transaction-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.5rem 0;
    border-bottom: 1px solid #e9ecef;
}

.transaction-item:last-child {
    border-bottom: none;
}

.actions-section {
    display: flex;
    gap: 1rem;
    justify-content: center;
    margin: 2rem 0;
    flex-wrap: wrap;
}

.invoice-footer {
    text-align: center;
    margin-top: 2rem;
}

.footer-text {
    color: #7f8c8d;
    margin: 0 0 0.5rem 0;
}

.footer-note {
    color: #95a5a6;
    font-size: 0.9rem;
    margin: 0;
}

@media (max-width: 768px) {
    .invoice-container {
        padding: 1rem;
    }

    .invoice-header {
        flex-direction: column;
        gap: 1rem;
    }

    .header-right {
        text-align: left;
    }

    .info-grid {
        grid-template-columns: 1fr;
    }

    .actions-section {
        flex-direction: column;
    }
}
</style>

<!--<template>-->
<!--    <Dialog-->
<!--                v-model:visible="displayModal"-->
<!--                modal-->
<!--                header="Просмотр документа"-->
<!--                :style="{ width: '85rem' }"-->
<!--                @hide="closeModal">-->

<!--    <Card class="invoice-card">-->
<!--        <template #title>-->
<!--            <div class="flex align-items-center justify-content-between">-->
<!--                <span>Счет #{{ invoice?.id }}</span>-->
<!--                <Tag :value="`Период: ${invoice?.period}`" severity="info" />-->
<!--            </div>-->
<!--        </template>-->

<!--        <template #content>-->
<!--            &lt;!&ndash; Основная информация &ndash;&gt;-->
<!--            <div class="grid">-->
<!--                <div class="col-12 md:col-6">-->
<!--                    <Card class="mb-3">-->
<!--                        <template #title>Информация о квартире</template>-->
<!--                        <template #content>-->
<!--                            <div class="flex flex-column gap-2">-->
<!--                                <div class="flex align-items-center gap-2">-->
<!--                                    <i class="pi pi-building text-primary"></i>-->
<!--                                    <span class="font-semibold">{{ invoice?.apartment?.building.name }}</span>-->
<!--                                </div>-->
<!--                                <div class="flex align-items-center gap-2">-->
<!--                                    <i class="pi pi-home text-primary"></i>-->
<!--                                    <span>Кв. {{ invoice?.apartment?.number }}, этаж {{ invoice?.apartment?.floor }}</span>-->
<!--                                </div>-->
<!--                                <div class="flex align-items-center gap-2">-->
<!--                                    <i class="pi pi-user text-primary"></i>-->
<!--                                    <span>{{ invoice?.owner.name }}</span>-->
<!--                                </div>-->
<!--                                <div class="flex align-items-center gap-2">-->
<!--                                    <i class="pi pi-phone text-primary"></i>-->
<!--                                    <span>{{ invoice?.owner.phone }}</span>-->
<!--                                </div>-->
<!--                            </div>-->
<!--                        </template>-->
<!--                    </Card>-->
<!--                </div>-->

<!--                <div class="col-12 md:col-6">-->
<!--                    <Card class="mb-3">-->
<!--                        <template #title>Финансовая информация</template>-->
<!--                        <template #content>-->
<!--                            <div class="flex flex-column gap-2">-->
<!--                                <div class="flex justify-content-between align-items-center">-->
<!--                                    <span class="font-medium">Общая сумма:</span>-->
<!--                                    <span class="text-2xl font-bold text-primary">{{ invoice?.total }} ₴</span>-->
<!--                                </div>-->
<!--                                <div class="flex justify-content-between align-items-center">-->
<!--                                    <span class="font-medium">Задолженность:</span>-->
<!--                                    <Tag :value="invoice?.debt" :severity="invoice?.debt > 0 ? 'danger' : 'success'" />-->
<!--                                </div>-->
<!--                                <div class="flex justify-content-between align-items-center">-->
<!--                                    <span class="font-medium">Статус:</span>-->
<!--                                    <Tag :value="invoice?.transactionEntry?.transaction.status === 'opened' ? 'Открыт' : 'Закрыт'"-->
<!--                                         :severity="invoice?.transactionEntry?.transaction.status === 'opened' ? 'warning' : 'success'" />-->
<!--                                </div>-->
<!--                                <div class="flex justify-content-between align-items-center">-->
<!--                                    <span class="font-medium">Дата создания:</span>-->
<!--                                    <span class="text-color-secondary">{{ formatDate(invoice?.created_at) }}</span>-->
<!--                                </div>-->
<!--                            </div>-->
<!--                        </template>-->
<!--                    </Card>-->
<!--                </div>-->
<!--            </div>-->

<!--            &lt;!&ndash; Услуги &ndash;&gt;-->
<!--            <Card>-->
<!--                <template #title>-->
<!--                    <div class="flex align-items-center gap-2">-->
<!--                        <i class="pi pi-list text-primary"></i>-->
<!--                        <span>Услуги и начисления</span>-->
<!--                    </div>-->
<!--                </template>-->
<!--                <template #content>-->
<!--                    <DataTable :value="invoice?.items" :rows="5" paginator>-->
<!--                        <Column field="tariff.id" header="ID" :sortable="true"></Column>-->
<!--                        <Column header="Услуга" :sortable="true">-->
<!--                            <template #body="slotProps">-->
<!--                                <div class="flex flex-column">-->
<!--                                    <span class="font-medium">Услуга #{{ slotProps.data.tariff.id }}</span>-->
<!--                                    <span class="text-color-secondary text-sm">Тип: {{ getServiceType(slotProps.data.tariff) }}</span>-->
<!--                                </div>-->
<!--                            </template>-->
<!--                        </Column>-->
<!--                        <Column field="quantity" header="Количество" :sortable="true">-->
<!--                            <template #body="slotProps">-->
<!--                                <div class="text-center">-->
<!--                                    <span class="font-medium">{{ slotProps.data.quantity }}</span>-->
<!--                                    <span class="text-color-secondary ml-1">{{ slotProps.data.measure.name }}</span>-->
<!--                                </div>-->
<!--                            </template>-->
<!--                        </Column>-->
<!--                        <Column field="amount" header="Сумма" :sortable="true">-->
<!--                            <template #body="slotProps">-->
<!--                                <div class="text-right">-->
<!--                                    <span class="font-bold">{{ slotProps.data.amount }} ₴</span>-->
<!--                                </div>-->
<!--                            </template>-->
<!--                        </Column>-->
<!--                        <Column header="Дата">-->
<!--                            <template #body="slotProps">-->
<!--                                <span class="text-color-secondary">{{ formatDate(slotProps.data.created_at) }}</span>-->
<!--                            </template>-->
<!--                        </Column>-->
<!--                    </DataTable>-->

<!--                    <Divider />-->

<!--                    <div class="flex justify-content-between align-items-center mt-3">-->
<!--                        <span class="text-2xl font-bold">Итого:</span>-->
<!--                        <span class="text-2xl font-bold text-primary">{{ invoice?.total }} ₴</span>-->
<!--                    </div>-->
<!--                </template>-->
<!--            </Card>-->

<!--            &lt;!&ndash; Транзакция &ndash;&gt;-->
<!--            <Card v-if="invoice?.transactionEntry" class="mt-3">-->
<!--                <template #title>-->
<!--                    <div class="flex align-items-center gap-2">-->
<!--                        <i class="pi pi-credit-card text-primary"></i>-->
<!--                        <span>Информация о транзакции</span>-->
<!--                    </div>-->
<!--                </template>-->
<!--                <template #content>-->
<!--                    <div class="grid">-->
<!--                        <div class="col-12 md:col-4">-->
<!--                            <div class="flex flex-column gap-1">-->
<!--                                <span class="text-color-secondary">Номер транзакции:</span>-->
<!--                                <span class="font-medium">#{{ invoice?.transactionEntry?.transaction.id }}</span>-->
<!--                            </div>-->
<!--                        </div>-->
<!--                        <div class="col-12 md:col-4">-->
<!--                            <div class="flex flex-column gap-1">-->
<!--                                <span class="text-color-secondary">Наименование:</span>-->
<!--                                <span class="font-medium">{{ invoice?.transactionEntry?.transaction.name }}</span>-->
<!--                            </div>-->
<!--                        </div>-->
<!--                        <div class="col-12 md:col-4">-->
<!--                            <div class="flex flex-column gap-1">-->
<!--                                <span class="text-color-secondary">Дата проведения:</span>-->
<!--                                <span class="font-medium">{{ formatDateTime(invoice?.transactionEntry?.transaction.posted) }}</span>-->
<!--                            </div>-->
<!--                        </div>-->
<!--                    </div>-->
<!--                </template>-->
<!--            </Card>-->
<!--        </template>-->

<!--        <template #footer>-->
<!--            <div class="flex gap-2 justify-content-end">-->
<!--                <Button label="Распечатать" icon="pi pi-print" severity="secondary" />-->
<!--                <Button label="Оплатить" icon="pi pi-credit-card" severity="success" />-->
<!--                <Button label="Экспорт PDF" icon="pi pi-file-pdf" severity="help" />-->
<!--            </div>-->
<!--        </template>-->
<!--    </Card>-->
<!--    </Dialog>-->
<!--</template>-->


<!--<style scoped>-->
<!--.invoice-card {-->
<!--    max-width: 1200px;-->
<!--    margin: 0 auto;-->
<!--}-->

<!--:deep(.p-card-title) {-->
<!--    font-size: 1.5rem;-->
<!--    font-weight: 600;-->
<!--}-->

<!--:deep(.p-card-content) {-->
<!--    padding: 1.5rem 0;-->
<!--}-->

<!--:deep(.p-card .p-card-content) {-->
<!--    padding: 0;-->
<!--}-->

<!--:deep(.p-card-body) {-->
<!--    padding: 2rem;-->
<!--}-->

<!--:deep(.p-card-footer) {-->
<!--    padding: 1.5rem 0 0 0;-->
<!--    border-top: 1px solid #e5e7eb;-->
<!--}-->

<!--.text-2xl {-->
<!--    font-size: 1.5rem;-->
<!--}-->

<!--.text-sm {-->
<!--    font-size: 0.875rem;-->
<!--}-->
<!--</style>-->

<!--<template>-->
<!--    <Dialog-->
<!--        v-model:visible="displayModal"-->
<!--        modal-->
<!--        header="Просмотр документа"-->
<!--        :style="{ width: '85rem' }"-->
<!--        @hide="closeModal"-->
<!--    >-->

<!--        <Card class="shadow-2xl border-round-2xl">-->
<!--            <template #title>-->
<!--                🧾 Счет №{{ invoice.id }} — Период: {{ invoice.period }}-->
<!--            </template>-->

<!--            <template #content>-->
<!--                <div class="grid gap-4">-->
<!--                    &lt;!&ndash; Основная инфа &ndash;&gt;-->
<!--                    <div class="col-12 md:col-6">-->
<!--                        <h3 class="text-lg">🏠 Квартира {{ invoice.apartment.number }}</h3>-->
<!--                        <p>-->
<!--                            Здание: <b>{{ invoice.apartment.building.name }}</b><br />-->
<!--                            Адрес: {{ invoice.apartment.building.address }}<br />-->
<!--                            Этаж: {{ invoice.apartment.floor }}<br />-->
<!--                            Площадь: {{ invoice.apartment.total_area }} м²<br />-->
<!--                            Жителей: {{ invoice.apartment.registered_residents }}-->
<!--                        </p>-->
<!--                    </div>-->

<!--                    <div class="col-12 md:col-6">-->
<!--                        <h3 class="text-lg">👤 Владелец</h3>-->
<!--                        <p>-->
<!--                            {{ invoice.owner.name }}<br />-->
<!--                            {{ invoice.owner.phone }}-->
<!--                        </p>-->

<!--                        <h3 class="text-lg mt-3">💳 Лицевой счет</h3>-->
<!--                        <p>-->
<!--                            № {{ invoice.apartment.financeAccount.number }}<br />-->
<!--                            Баланс:-->
<!--                            <Tag-->
<!--                                :value="invoice.apartment.financeAccount.balance"-->
<!--                                severity="info"-->
<!--                            />-->
<!--                        </p>-->
<!--                    </div>-->
<!--                </div>-->

<!--                <Divider />-->

<!--                &lt;!&ndash; Таб с услугами &ndash;&gt;-->
<!--                <TabView>-->
<!--                    <TabPanel header="📋 Услуги">-->
<!--                        <DataTable-->
<!--                            :value="invoice.items"-->
<!--                            stripedRows-->
<!--                            responsiveLayout="scroll"-->
<!--                        >-->
<!--                            <Column field="id" header="ID" />-->
<!--                            <Column field="quantity" header="Кол-во" />-->
<!--                            <Column-->
<!--                                field="measure.name"-->
<!--                                header="Ед."-->
<!--                                :body="row => row.measure.name"-->
<!--                            />-->
<!--                            <Column-->
<!--                                field="amount"-->
<!--                                header="Сумма"-->
<!--                                :body="row => row.amount + ' ₴'"-->
<!--                            />-->
<!--                        </DataTable>-->
<!--                    </TabPanel>-->

<!--                    <TabPanel header="💰 Итоги">-->
<!--                        <p class="text-xl">-->
<!--                            Общая сумма:-->
<!--                            <Tag severity="success" :value="invoice.total + ' ₴'" />-->
<!--                        </p>-->
<!--                        <p class="text-xl mt-2">-->
<!--                            Долг:-->
<!--                            <Tag severity="danger" :value="invoice.debt + ' ₴'" />-->
<!--                        </p>-->
<!--                    </TabPanel>-->

<!--                    <TabPanel header="📑 Транзакция">-->
<!--                        <Accordion>-->
<!--                            <AccordionTab :header="invoice.transactionEntry.transaction.name">-->
<!--                                <p>-->
<!--                                    Статус:-->
<!--                                    <Tag severity="warning">-->
<!--                                        {{ invoice.transactionEntry.transaction.status }}-->
<!--                                    </Tag>-->
<!--                                </p>-->
<!--                                <p>Дата: {{ invoice.transactionEntry.transaction.posted }}</p>-->
<!--                            </AccordionTab>-->
<!--                        </Accordion>-->
<!--                    </TabPanel>-->
<!--                </TabView>-->
<!--            </template>-->
<!--        </Card>-->


<!--        <div class="flex justify-end gap-2 mt-6">-->
<!--            <Button type="button" label="Закрыть" severity="secondary" @click="closeModal"/>-->
<!--        </div>-->


<!--    </Dialog>-->
<!--</template>-->


