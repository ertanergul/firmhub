<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Trusted Proxies
    |--------------------------------------------------------------------------
    |
    | Comma-separated IP addresses or CIDR ranges of the reverse proxies or
    | load balancers in front of the application. X-Forwarded-* headers are
    | honored only from these addresses. Leave empty when no proxy is used.
    |
    */

    'proxies' => env('TRUSTED_PROXIES') ?: null,

];
