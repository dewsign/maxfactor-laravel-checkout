<?php

return [
    "driver" => "App\Checkout",
    "postage" => "App\Postage",
    "minimum_order" => env('MINIMUM_ORDER_VALUE', 1.00),
    "checkout_stages" => [
        'DEFAULT' => '1',
        'SHIPPING' => '2',
        'PAYMENT' => '3',
        'COMPLETE' => '4',
        'PAYPALCOMPLETE' => '4',
    ],
];
