<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

beforeEach(function () {
    Route::get('/_test/client-ip', fn (Request $request) => $request->ip());
});

test('forwarded headers from untrusted addresses are ignored', function () {
    $response = $this->withServerVariables(['REMOTE_ADDR' => '203.0.113.5'])
        ->withHeader('X-Forwarded-For', '198.51.100.7')
        ->get('/_test/client-ip');

    $response->assertOk()->assertContent('203.0.113.5');
});

test('forwarded headers from configured proxies are honored', function () {
    config(['trustedproxy.proxies' => '10.0.0.1']);

    $response = $this->withServerVariables(['REMOTE_ADDR' => '10.0.0.1'])
        ->withHeader('X-Forwarded-For', '198.51.100.7')
        ->get('/_test/client-ip');

    $response->assertOk()->assertContent('198.51.100.7');
});
