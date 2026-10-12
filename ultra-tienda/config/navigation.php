<?php

/*
 |---------------------------------------------------------------------
 | Navegación del panel administrativo
 |---------------------------------------------------------------------
 | Cada elemento admite:
 |   - label : texto visible
 |   - icon  : nombre del icono lucide (recurso: resources/views/components/icon.blade.php)
 |   - route : nombre de ruta Laravel (alternativa a url)
 |   - url   : URL absoluta relativa (alternativa a route)
 */

return [
    'groups' => [
        [
            'title' => 'General',
            'items' => [
                ['label' => 'Dashboard', 'icon' => 'layout-dashboard', 'url' => '/'],
            ],
        ],
        [
            'title' => 'Catálogo',
            'items' => [
                ['label' => 'Productos', 'icon' => 'package', 'route' => 'products.index'],
                ['label' => 'Categorías', 'icon' => 'folder-tree', 'route' => 'categories.index'],
            ],
        ],
        [
            'title' => 'Ventas',
            'items' => [
                ['label' => 'Pedidos', 'icon' => 'shopping-bag', 'route' => 'orders.index'],
                ['label' => 'Detalles de pedido', 'icon' => 'list', 'route' => 'order-details.index'],
                ['label' => 'Pagos de pedidos', 'icon' => 'credit-card', 'route' => 'order-payments.index'],
            ],
        ],
        [
            'title' => 'Administración',
            'items' => [
                ['label' => 'Usuarios', 'icon' => 'users', 'route' => 'users.index'],
                ['label' => 'Roles', 'icon' => 'shield', 'route' => 'roles.index'],
            ],
        ],
    ],
];
