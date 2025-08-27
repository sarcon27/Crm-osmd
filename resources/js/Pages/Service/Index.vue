<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import TariffsTab from './TariffTabs.vue'
import MeasuresTab from './MeasuresTab.vue'
import ServicesTab from './ServicesTab.vue'
import {Head} from "@inertiajs/vue3";

const props = defineProps({
    title: String,
    services: Object,
    tariffs: Object,
    measures: Object,
    filters: Object,

});
</script>

<template>
    <app-layout>
        <Head :title="title"/>
        <div class="card">
            <TabView :activeIndex="0">
                <TabPanel header="Услуги">
                    <ServicesTab :services="services"/>
                </TabPanel>
                <TabPanel header="Тарифы">
                    <TariffsTab :tariffs="tariffs" :measures="measures" :services="services" :filters="filters"/>
                </TabPanel>
                <TabPanel header="Единицы измерений">
                    <MeasuresTab :measures="measures"/>
                </TabPanel>
            </TabView>
        </div>
    </app-layout>
</template>

<style scoped lang="scss">

</style>
