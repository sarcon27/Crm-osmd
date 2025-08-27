import { reactive, watch } from 'vue'
import { useForm } from '@inertiajs/vue3'
import { useToast } from 'primevue/usetoast'

export function useResourceForm(props, emit, options = {}) {
    const {
        resourceName,
        routePrefix = 'dashboard',
        defaultFormData = {},
        beforeSubmit = (data) => data,
        onSuccess = () => {},
        onInitData = null,
        propKey = resourceName // дефолтный ключ
    } = options

    const toast = useToast()

    // Хранение состояния для нескольких модалок
    const modals = reactive({})

    // Получаем или создаем модалку по ключу
    const getModal = (key) => {
        if (!modals[key]) {
            modals[key] = {
                display: false,
                form: useForm({ id: null, ...defaultFormData })
            }
        }
        return modals[key]
    }

    // Инициализация данных формы
    const initData = (key, data) => {
        const modal = getModal(key)
        if (typeof onInitData === 'function') {
            onInitData(data, modal.form)
        } else {
            Object.assign(modal.form, data)
        }
    }

    // Открытие модалки
    const openModal = (key, data = null) => {
        const modal = getModal(key)
        modal.form.reset()
        modal.form.clearErrors()
        modal.display = true
        if (data) initData(key, data)
        emit('update:show', true)
    }

    // Закрытие модалки
    const closeModal = (key) => {
        const modal = getModal(key)
        modal.display = false
        modal.form.clearErrors()
        emit('update:show', false)
    }

    // Отправка формы
    const submit = (key) => {
        const modal = getModal(key)
        const url = modal.form.id
            ? route(`${routePrefix}.${resourceName}.update`, modal.form.id)
            : route(`${routePrefix}.${resourceName}.store`)
        const method = modal.form.id ? 'put' : 'post'

        modal.form.transform(beforeSubmit)[method](url, {
            preserveScroll: true,
            onSuccess: () => {
                toast.add({
                    severity: 'success',
                    summary: 'Успешно',
                    detail: modal.form.id ? 'Данные обновлены' : 'Данные добавлены',
                    life: 3000
                })
                closeModal(key)
                emit('refresh')
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

    // Проверка ошибок
    const hasError = (key, path) => Boolean(getModal(key).form.errors?.[path])
    const getError = (key, path) => getModal(key).form.errors?.[path] || ''

    // Watch на props для автоматического открытия модалки с данными
    watch(
        () => props[propKey],
        (value) => {
            if (value) openModal(propKey, value)
        },
        { immediate: true } // сразу срабатывает, если данные уже есть
    )

    return {
        modals,
        getModal,
        openModal,
        closeModal,
        submit,
        hasError,
        getError
    }
}
