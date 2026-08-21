<?php

use App\Models\User;

it('permite login com email', function () {
    $user = createUserWithRole('admin', [
        'email' => 'admin@test.com',
        'password' => bcrypt('password'),
    ]);

    $response = $this->postJson('/login', [
        'login' => 'admin@test.com',
        'password' => 'password',
    ]);

    $response->assertOk();
    $this->assertAuthenticated();
});

it('permite login com username', function () {
    $user = createUserWithRole('admin', [
        'username' => 'adminuser',
        'password' => bcrypt('password'),
    ]);

    $response = $this->postJson('/login', [
        'login' => 'adminuser',
        'password' => 'password',
    ]);

    $response->assertOk();
    $this->assertAuthenticated();
});

it('rejeita credenciais invalidas', function () {
    createUserWithRole('admin', [
        'email' => 'admin@test.com',
        'password' => bcrypt('password'),
    ]);

    $response = $this->postJson('/login', [
        'login' => 'admin@test.com',
        'password' => 'errada',
    ]);

    $response->assertStatus(422);
    $this->assertGuest();
});