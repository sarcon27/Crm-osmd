<?php

namespace App\Services;

class MenuService
{
    public static function getMenu(): array
    {
        return [
            [
                'items' => [
                    ['label' => 'Статистика dev', 'icon' => 'pi pi-fw pi-chart-bar', 'to' => route('dashboard.index')],
                    //  ['label' => 'Касса dev', 'icon' => 'pi pi-fw pi-money-bill', 'to' => route('dashboard.index')],
                    ['label' => 'Проводки', 'icon' => 'pi pi-fw pi-receipt', 'to' => route('dashboard.finance-transactions.index')],
                    ['label' => 'Документы', 'icon' => 'pi pi-fw pi-receipt', 'to' => route('dashboard.invoices.index')],
                    ['label' => 'Счета', 'icon' => 'pi pi-fw pi-book', 'to' => route('dashboard.finance-accounts.index')],
                    ['separator' => true],
                    ['label' => 'Квартиры', 'icon' => 'pi pi-fw pi-objects-column', 'to' => route('dashboard.apartments.index')],
                    ['label' => 'Жители', 'icon' => 'pi pi-fw pi-users', 'to' => route('dashboard.owners.index')],
                    ['label' => 'Дома', 'icon' => 'pi pi-fw pi-building-columns', 'to' => route('dashboard.buildings.index')],
                    ['separator' => true],
                    ['label' => 'Счетчики', 'icon' => 'pi pi-fw pi-gauge', 'to' => route('dashboard.meters.index')],
                    ['label' => 'Услуги', 'icon' => 'pi pi-fw pi-users', 'to' => route('dashboard.services.index')],
                    ['separator' => true],
                    // ['separator' => true],
                    //     ['label' => 'Заявки dev', 'icon' => 'pi pi-fw pi-ticket', 'to' => '/'],
                    ['separator' => true],
                    [
                        'label' => 'Настройки',
                        'icon' => 'pi pi-fw pi-cog',
                        'items' => [
                            ['label' => 'Кабинет', 'icon' => 'pi pi-fw pi-times-circle', 'to' => route('dashboard.settings.index')],
                            //          ['label' => 'Роли  dev', 'icon' => 'pi pi-fw pi-sign-in', 'to' => '/auth/login'],
                            //        ['label' => 'Пользователи  dev', 'icon' => 'pi pi-fw pi-lock', 'to' => '/auth/access'],
                        ],
                    ],
                ],
            ],
        ];
    }
}
