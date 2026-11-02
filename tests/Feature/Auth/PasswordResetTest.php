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
            ->assertSessionHas('status', __('If an account exists for that email address, a password reset link has been sent.'));
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
        ->and($links[0])->toStartWith(config('app.url').'/reset-password/');
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
