<?php

use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Support\Facades\Notification;
use Laravel\Fortify\Features;

beforeEach(function () {
    $this->skipUnlessFortifyHas(Features::resetPasswords());
});

test('reset password link screen can be rendered', function () {
    $response = $this->get(route('password.request'));

    $response->assertOk();
});

test('reset password link can be requested', function () {
    Notification::fake();

    $user = User::factory()->create();

    $this->post(route('password.request'), ['email' => $user->email]);

    Notification::assertSentTo($user, ResetPassword::class);
});

test('reset password link response does not reveal whether an account exists', function () {
    Notification::fake();

    $user = User::factory()->create();

    $knownResponse = $this->from(route('password.request'))
        ->post(route('password.email'), ['email' => $user->email]);

    $unknownResponse = $this->from(route('password.request'))
        ->post(route('password.email'), ['email' => 'nobody@example.com']);

    foreach ([$knownResponse, $unknownResponse] as $response) {
        $response->assertRedirect(route('password.request'))
            ->assertSessionHasNoErrors()
            ->assertSessionHas('status', __('pages.authentication.forgot_password.link_sent'));
    }

    Notification::assertSentTo($user, ResetPassword::class);
    Notification::assertSentTimes(ResetPassword::class, 1);
});

test('reset password link json response does not reveal whether an account exists', function () {
    Notification::fake();

    $user = User::factory()->create();

    $knownResponse = $this->postJson(route('password.email'), ['email' => $user->email]);
    $unknownResponse = $this->postJson(route('password.email'), ['email' => 'nobody@example.com']);

    $knownResponse->assertOk();
    $unknownResponse->assertOk();

    expect($unknownResponse->json())->toBe($knownResponse->json());
});

test('reset password screen can be rendered', function () {
    Notification::fake();

    $user = User::factory()->create();

    $this->post(route('password.request'), ['email' => $user->email]);

    Notification::assertSentTo($user, ResetPassword::class, function ($notification) {
        $response = $this->get(route('password.reset', $notification->token));

        $response->assertOk();

        return true;
    });
});

test('password can be reset with valid token', function () {
    Notification::fake();

    $user = User::factory()->create();

    $this->post(route('password.request'), ['email' => $user->email]);

    Notification::assertSentTo($user, ResetPassword::class, function ($notification) use ($user) {
        $response = $this->post(route('password.update'), [
            'token' => $notification->token,
            'email' => $user->email,
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('login', absolute: false));

        return true;
    });
});

test('reset password links ignore forwarded hosts from untrusted addresses', function () {
    $user = User::factory()->create();

    $this->withServerVariables(['REMOTE_ADDR' => '203.0.113.5'])
        ->withHeader('X-Forwarded-Host', 'evil.example')
        ->post(route('password.email'), ['email' => $user->email]);

    $links = sentPasswordResetLinks();

    expect($links)->toHaveCount(1)
        ->and($links[0])->toStartWith(config('app.url').'/en/reset-password/');
});

test('reset password requests forwarded by a proxy for another host are not handled', function () {
    config(['trustedproxy.proxies' => '10.0.0.1']);

    $user = User::factory()->create();

    $response = $this->withServerVariables(['REMOTE_ADDR' => '10.0.0.1'])
        ->withHeader('X-Forwarded-Host', 'evil.example')
        ->post(route('password.email'), ['email' => $user->email]);

    $response->assertNotFound();

    expect(sentPasswordResetLinks())->toBeEmpty();
});

test('reset password emails are sent in the user\'s language with links in that language', function () {
    $user = User::factory()->create(['locale' => 'tr']);

    $this->post(route('password.email', ['locale' => 'en']), ['email' => $user->email]);

    expect(sentMailSubjects())->toBe(['Şifrenizi sıfırlayın'])
        ->and(sentPasswordResetLinks()[0])->toStartWith(config('app.url').'/tr/reset-password/')
        ->and(route('home'))->toBe(config('app.url').'/en');
});

test('reset password emails fall back to the default language for unsupported stored languages', function (string $locale) {
    $user = User::factory()->create(['locale' => $locale]);

    $this->post(route('password.email'), ['email' => $user->email]);

    expect(sentMailSubjects())->toBe(['Reset your password']);
})->with([
    'unknown language' => 'xx',
    'path characters' => 'tr/..',
]);

/**
 * Extract the password reset links from the emails captured by the array mail transport.
 *
 * @return array<int, string>
 */
function sentPasswordResetLinks(): array
{
    return app('mailer')->getSymfonyTransport()->messages()
        ->flatMap(function ($message): array {
            preg_match_all('#https?://[^\s"<>]+/reset-password/[^\s"<>]+#', (string) $message->getOriginalMessage()->getHtmlBody(), $matches);

            return array_unique($matches[0]);
        })
        ->values()
        ->all();
}

/**
 * The subjects of the emails captured by the array mail transport.
 *
 * @return array<int, string|null>
 */
function sentMailSubjects(): array
{
    return app('mailer')->getSymfonyTransport()->messages()
        ->map(fn ($message): ?string => $message->getOriginalMessage()->getSubject())
        ->values()
        ->all();
}
