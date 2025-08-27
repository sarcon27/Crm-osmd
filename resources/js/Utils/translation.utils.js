import {usePage} from "@inertiajs/vue3"

export const trans = (key) => {
    const translations = usePage().props.translations

    return key.split('.').reduce((acc, part) => {
        if (acc && acc[part] !== undefined) {
            return acc[part]
        }
        return undefined
    }, translations) ?? key
}
