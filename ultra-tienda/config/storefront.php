<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Moneda de la tienda
    |--------------------------------------------------------------------------
    |
    | Código ISO 4217 con el que la tienda pública presenta los precios.
    |
    */

    'currency' => env('STOREFRONT_CURRENCY', 'COP'),

    /*
    |--------------------------------------------------------------------------
    | Envío
    |--------------------------------------------------------------------------
    |
    | El costo de envío se cobra solo cuando el subtotal no alcanza el umbral
    | de envío gratis. El total de cada pedido se calcula en el servidor, por
    | lo que estos valores siempre prevalecen sobre lo que envíe el cliente.
    |
    */

    'shipping_cost' => (float) env('STOREFRONT_SHIPPING_COST', 15000),

    'free_shipping_threshold' => (float) env('STOREFRONT_FREE_SHIPPING_THRESHOLD', 500000),

    /*
    |--------------------------------------------------------------------------
    | Métodos de pago
    |--------------------------------------------------------------------------
    |
    | Lista de métodos aceptados al crear un pedido desde la tienda pública.
    |
    */

    'payment_methods' => [
        'efectivo',
        'transferencia',
        'contraentrega',
    ],

    /*
    |--------------------------------------------------------------------------
    | Paginación del catálogo
    |--------------------------------------------------------------------------
    */

    'per_page' => 50,

];
