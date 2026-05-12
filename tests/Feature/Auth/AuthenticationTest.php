<?php

declare(strict_types=1);

use App\Models\User;
use Laravel\Fortify\Features;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\User as SocialiteUser;

test('login screen can be rendered', function (): void {
    $response = $this->get(route('login'));

    $response->assertOk();
});

test('users can authenticate using the login screen', function (): void {
    $user = User::factory()->create();

    $response = $this->post(route('login.store'), [
        'email' => $user->email,
        'password' => 'password',
    ]);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect(route('home', absolute: false));

    $this->assertAuthenticated();
});

test('users can not authenticate with invalid password', function (): void {
    $user = User::factory()->create();

    $response = $this->post(route('login.store'), [
        'email' => $user->email,
        'password' => 'wrong-password',
    ]);

    $response->assertSessionHasErrorsIn('email');

    $this->assertGuest();
});

test('users with two factor enabled are redirected to two factor challenge', function (): void {
    $this->skipUnlessFortifyHas(Features::twoFactorAuthentication());

    Features::twoFactorAuthentication([
        'confirm' => true,
        'confirmPassword' => true,
    ]);

    $user = User::factory()->withTwoFactor()->create();

    $response = $this->post(route('login.store'), [
        'email' => $user->email,
        'password' => 'password',
    ]);

    $response->assertRedirect(route('two-factor.login'));
    $this->assertGuest();
});

test('users can logout', function (): void {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post(route('logout'));

    $response->assertRedirect(route('home'));

    $this->assertGuest();
});

test('users can authenticate with google', function (): void {
    $socialiteUser = new SocialiteUser()->map([
        'id' => 'google-123',
        'name' => 'Google User',
        'email' => 'google@example.com',
    ]);
    $provider = Mockery::mock();

    $provider->shouldReceive('user')->once()->andReturn($socialiteUser);
    Socialite::shouldReceive('driver')->once()->with('google')->andReturn($provider);

    $response = $this->get(route('auth.google.callback'));

    $response->assertRedirect(route('home'));
    $this->assertAuthenticated();

    expect(User::query()->where('email', 'google@example.com')->first())
        ->not->toBeNull()
        ->google_id->toBe('google-123');
});

test('google auth links existing user by email', function (): void {
    $user = User::factory()->create([
        'email' => 'existing@example.com',
        'google_id' => null,
    ]);
    $socialiteUser = new SocialiteUser()->map([
        'id' => 'google-existing',
        'name' => 'Existing User',
        'email' => 'existing@example.com',
    ]);
    $provider = Mockery::mock();

    $provider->shouldReceive('user')->once()->andReturn($socialiteUser);
    Socialite::shouldReceive('driver')->once()->with('google')->andReturn($provider);

    $this->get(route('auth.google.callback'))->assertRedirect(route('home'));

    $user->refresh();

    expect($user->google_id)->toBe('google-existing');
    $this->assertAuthenticatedAs($user);
});
