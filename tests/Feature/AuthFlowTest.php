<?php

use App\Models\User;
use App\Models\Role;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;

it('registers a new user and authenticates them', function () {
    $response = $this->post('/register', [
        'name' => 'Иван Иванов',
        'email' => 'ivan@example.com',
        'phone_number' => '+79990000000',
        'password' => 'secret123',
        'password_confirmation' => 'secret123',
    ]);

    $response->assertRedirect('/dashboard');
    $this->assertAuthenticated();

    $user = User::where('email', 'ivan@example.com')->first();

    expect($user)->not->toBeNull();
    expect($user->name)->toBe('Иван Иванов');
    expect($user->phone_number)->toBe('+79990000000');
    expect(Hash::check('secret123', $user->password))->toBeTrue();
});

it('logs in an existing user', function () {
    $user = User::factory()->create([
        'email' => 'reader@example.com',
        'password' => Hash::make('secret123'),
    ]);

    $response = $this->post('/login', [
        'email' => $user->email,
        'password' => 'secret123',
    ]);

    $response->assertRedirect(route('dashboard'));
    $this->assertAuthenticatedAs($user);
});

it('logs in an admin and redirects to admin panel', function () {
    $adminRole = Role::create([
        'role_name' => 'admin',
    ]);

    $admin = User::factory()->create([
        'email' => 'admin@example.com',
        'password' => Hash::make('secret123'),
        'id_role' => $adminRole->getKey(),
    ]);

    $response = $this->post('/login', [
        'email' => $admin->email,
        'password' => 'secret123',
    ]);

    $response->assertRedirect(route('admin.index'));
    $this->assertAuthenticatedAs($admin);
});

it('sends a password reset notification', function () {
    Notification::fake();

    $user = User::factory()->create([
        'email' => 'reader@example.com',
    ]);

    $response = $this->post('/forgot-password', [
        'email' => $user->email,
    ]);

    $response->assertSessionHas('status', 'Если такой email существует, ссылка для сброса уже отправлена.');

    Notification::assertSentTo($user, ResetPassword::class);
});

it('returns the same password reset response for an unknown email', function () {
    Notification::fake();

    $response = $this->post('/forgot-password', [
        'email' => 'missing@example.com',
    ]);

    $response->assertSessionHas('status', 'Если такой email существует, ссылка для сброса уже отправлена.');

    Notification::assertNothingSent();
});

it('shows the reset password form and updates the password', function () {
    $user = User::factory()->create([
        'email' => 'reader@example.com',
        'password' => Hash::make('old-password'),
    ]);

    $token = Password::broker()->createToken($user);

    $this->get(route('password.reset', ['token' => $token, 'email' => $user->email]))
        ->assertOk()
        ->assertSee('Новый пароль');

    $response = $this->post('/reset-password', [
        'token' => $token,
        'email' => $user->email,
        'password' => 'new-secret123',
        'password_confirmation' => 'new-secret123',
    ]);

    $response->assertRedirect(route('login'));
    $response->assertSessionHas('status');

    expect(Hash::check('new-secret123', $user->fresh()->password))->toBeTrue();
});

it('tops up the user balance from the profile page', function () {
    $user = User::factory()->create([
        'balance' => 500.00,
    ]);

    $response = $this->actingAs($user)->post(route('balance.topup'), [
        'amount' => 1500,
        'payment_method' => 'sbp',
    ]);

    $response->assertRedirect(route('dashboard'));
    $response->assertSessionHas('status', 'Баланс пополнен на 1 500.00 ₽.');

    $this->assertDatabaseHas('users', [
        'id_users' => $user->getKey(),
        'balance' => 2000.00,
    ]);
});
