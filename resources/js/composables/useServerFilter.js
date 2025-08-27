import { router } from '@inertiajs/vue3';
import { ref } from 'vue';

export const useServerTable = (routeName, filtersRef, metaRef) => {

    const updateTable = (extraQuery = {}) => {
        const query = {
            ...Object.fromEntries(
                Object.entries(filtersRef.value || {})
                    .filter(([_, v]) => v !== null && v !== '')
                    .map(([k, v]) => [`filter[${k}]`, v])
            ),
            ...extraQuery
        };

        router.get(route(routeName), query, {
            preserveScroll: true,
            preserveState: true
        });
    };

    const onPage = (event) => {
        // event.page — нумерация с 0
        updateTable({ page: (event.page || 0) + 1, per_page: metaRef.value?.per_page || 10 });
    };

    const onSort = (event) => {
        const sortField = event.sortField;
        const sortOrder = event.sortOrder === 1 ? sortField : `-${sortField}`;
        updateTable({ sort: sortOrder, page: metaRef.value?.current_page || 1, per_page: metaRef.value?.per_page || 10 });
    };

    const onServerFilter = (field, value) => {
        filtersRef.value[field] = value;
        updateTable({ page: 1 }); // при фильтре начинаем с первой страницы
    };

    return { onPage, onSort, onServerFilter };
};
