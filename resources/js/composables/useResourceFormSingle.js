import {ref, watch} from 'vue'
import {useForm} from '@inertiajs/vue3'
import {useToast} from 'primevue/usetoast'

export function useResourceForm(props, emit, options = {}) {
    const {
        resourceName,
        routePrefix = 'dashboard',
        defaultFormData = {},
        beforeSubmit = (data) => data,
        onSuccess = () => {
        },
        propKey = resourceName,
        onInitData = null,
        resetRefs = []
    } = options

    const toast = useToast()
    const displayModal = ref(false)

    // Инициализация формы
    const form = useForm({
        id: null,
        ...defaultFormData
    })

    // Маршруты
    const routes = {
        store: route(`${routePrefix}.${resourceName}.store`),
        update: (id) => route(`${routePrefix}.${resourceName}.update`, id)
    }

    // Методы валидации
    const hasError = (path) => Boolean(form.errors?.[path])
    const getError = (path) => form.errors?.[path] || ''

    // Управление модальным окном
    const closeModal = () => {
        form.reset()
        displayModal.value = false
        emit('update:show', false)
        form.clearErrors()

        resetRefs.forEach(refOrFunc => {
            if (typeof refOrFunc === 'function') {
                refOrFunc();
            } else if ('value' in refOrFunc) {
                refOrFunc.value = null;
            }
        });
    }

    const openModal = (data = null) => {
        form.reset()
        form.clearErrors()
        displayModal.value = true
        initData(data)
    }

    const initData = (data) => {
        if (!data) return

        if (onInitData && typeof onInitData === 'function') {
            onInitData(data, form) // кастомная инициализация
        } else {
            Object.assign(form, data) // стандартная инициализация
        }
    }

    // Обработчик отправки формы
    const submit = (closeOnSuccess = true) => {
        const url = form.id ? routes.update(form.id) : routes.store
        const method = form.id ? 'put' : 'post'

        form.transform(beforeSubmit)[method](url, {
            preserveScroll: true,
            onSuccess: () => {
                toast.add({
                    severity: 'success',
                    summary: 'Успешно',
                    detail: form.id ? 'Данные обновлены' : 'Данные добавлены',
                    life: 3000
                })
                if (closeOnSuccess) {
                    closeModal()
                    emit('refresh')
                }
                onSuccess()
            },
            onError: () => {
                toast.add({
                    severity: 'error',
                    summary: 'Ошибка',
                    detail: 'Проверьте поля формы',
                    life: 3000
                })
            }
        })
    }

    // Наблюдатель за открытием/закрытием модального окна
    watch(() => props.show, (value) => {
        displayModal.value = value
        if (value) {
            // Если переданы данные для редактирования
            if (props[propKey]) {
                form.reset()
                // Object.assign(form, props[propKey])
                openModal(props[propKey])
            } else {
                // Режим создания
                form.reset()
            }
        }
    })

    watch(() => props[propKey], (value) => {
        if (displayModal.value && value) {
            openModal(value)
        }
    })

    return {
        displayModal,
        closeModal,
        openModal,
        form,
        hasError,
        getError,
        submit,
        routes
    }
}
