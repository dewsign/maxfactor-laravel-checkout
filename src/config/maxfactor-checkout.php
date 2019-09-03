<?php

return [
    "driver" => "App\Checkout",
    "postage" => "App\Postage",
    "pca_key" => env('PCA_KEY'),
    "minimum_order" => env('MINIMUM_ORDER_VALUE', 1.00),
    "checkout_stages" => [
        'DEFAULT' => '1',
        'SHIPPING' => '2',
        'PAYMENT' => '3',
        'SCA' => '4',
        'COMPLETE' => '4',
        'PAYPALCOMPLETE' => '4',
    ],
];
