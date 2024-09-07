<?php
return [
    [
        'class' => 'yii\rest\UrlRule',
        'controller' => 'user',
        'extraPatterns' => [
            'POST login' => 'login',
            'OPTIONS login' => 'options',
            'GET get-me' => 'get-me',
            'OPTIONS get-me' => 'options',
        ],
    ],
    [
        'class' => 'yii\rest\UrlRule',
        'controller' => 'unit',
        'extraPatterns' => [
        ],
    ],
    [
        'class' => 'yii\rest\UrlRule',
        'controller' => 'category',
        'extraPatterns' => [
        ],
    ],
    [
        'class' => 'yii\rest\UrlRule',
        'controller' => 'product',
        'extraPatterns' => [
            'GET find-by-barcode/<barcode:>' => 'find-by-barcode',
            'OPTIONS find-by-barcode/<barcode:>' => 'options',
            'POST add-amount/<id:>' => 'add-amount',
            'OPTIONS add-amount/<id:>' => 'options',
        ],
    ],
    [
        'class' => 'yii\rest\UrlRule',
        'controller' => 'product-history',
        'extraPatterns' => [
            ],
    ],
    [
        'class' => 'yii\rest\UrlRule',
        'controller' => 'product-list',
        'extraPatterns' => [
            'POST add-product/<id:>' => 'add-product',
            'OPTIONS add-product/<id:>' => 'options',
            'POST accept/<id:>' => 'accept',
            'OPTIONS accept/<id:>' => 'options',
        ],
    ],
    [
        'class' => 'yii\rest\UrlRule',
        'controller' => 'customer',
        'extraPatterns' => [
        ],
    ],
    [
        'class' => 'yii\rest\UrlRule',
        'controller' => 'order',
        'extraPatterns' => [
        ],
    ],
    [
        'class' => 'yii\rest\UrlRule',
        'controller' => 'setting',
        'extraPatterns' => [
        ],
    ]
];