<?php

return [
    "driver" => "App\Checkout",
    "postage" => "App\Postage",
    "minimum_order" => env('MINIMUM_ORDER_VALUE', 35.00),
];
