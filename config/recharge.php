<?php

return [
    'base_url' => env('RECHARGE_BASE_URL', 'https://api.rechargeapps.com'),
    'api_version' => env('RECHARGE_API_VERSION', '2021-11'),
    'timeout' => (int) env('RECHARGE_TIMEOUT', 15),
    'retry_times' => (int) env('RECHARGE_RETRY_TIMES', 2),
];
