<?php

return [
    'g1' => [
        'base' => env('G1_BASE', 'http://gateways:3001'),
        'email' => env('G1_EMAIL'),
        'token' => env('G1_TOKEN'),
    ],
    'g2' => [
        'base' => env('G2_BASE', 'http://gateways:3002'),
        'auth_token' => env('G2_AUTH_TOKEN'),
        'auth_secret' => env('G2_AUTH_SECRET'),
    ],
];
