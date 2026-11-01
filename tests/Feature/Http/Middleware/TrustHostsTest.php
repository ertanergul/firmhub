<?php

use Illuminate\Http\Middleware\TrustHosts;

/**
 * The framework skips the trusted host check in the local environment and in tests,
 * so these tests assert the configured patterns that are enforced everywhere else.
 */
function isTrustedHost(string $host): bool
{
    foreach (app(TrustHosts::class)->hosts() as $pattern) {
        if (preg_match('{'.$pattern.'}i', $host) === 1) {
            return true;
        }
    }

    return false;
}

test('the platform host and tenant subdomains are trusted', function (string $host) {
    expect(isTrustedHost($host))->toBeTrue();
})->with([
    'platform host' => 'localhost',
    'tenant subdomain' => 'acme.localhost',
]);

test('other hosts are not trusted', function (string $host) {
    expect(isTrustedHost($host))->toBeFalse();
})->with([
    'foreign domain' => 'evil.example',
    'multi-level subdomain' => 'a.b.localhost',
    'tenant domain suffix trick' => 'acme.localhost.evil.example',
]);

test('trusted hosts follow the configured application host', function () {
    config(['app.host' => 'firmhub.test']);

    expect(isTrustedHost('firmhub.test'))->toBeTrue()
        ->and(isTrustedHost('acme.firmhub.test'))->toBeTrue()
        ->and(isTrustedHost('localhost'))->toBeFalse();
});
