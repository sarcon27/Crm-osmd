// usePagination.js
import {router} from '@inertiajs/vue3';

export const usePagination = () => {
    const onPage = (event, meta, routeName) => {
        const targetPage = event.page + 1;
        const link = meta.links.find(l => l.label == targetPage);

        if (link?.url) {
            router.visit(link.url, {
                preserveScroll: true,
                preserveState: true
            });
        }
    };

    const onSort = (event, meta, routeName) => {
        const sortField = event.sortField;
        const sortOrder = event.sortOrder === 1 ? sortField : `-${sortField}`;

        router.get(route(routeName), {
            sort: sortOrder,
            page: meta.current_page,
            per_page: meta.per_page
        }, {
            preserveScroll: true,
            preserveState: true
        });
    };

    return {onPage, onSort};
};
