// composables/useServerTable.js
import { router } from '@inertiajs/vue3';

export const useServerTable = (routeName, filtersRef, meta) => {

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
        updateTable({ page: event.page + 1, per_page: meta.per_page });
    };

    const onSort = (event) => {
        const sortField = event.sortField;
        const sortOrder = event.sortOrder === 1 ? sortField : `-${sortField}`;
        updateTable({ sort: sortOrder, page: meta.current_page, per_page: meta.per_page });
    };

    const onServerFilter = (field, value) => {
        filtersRef.value[field] = value;
        updateTable({ page: 1 }); // при фильтре всегда начинаем с первой страницы
    };

    return { onPage, onSort, onServerFilter };
};
