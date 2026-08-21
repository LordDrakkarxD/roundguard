<?php

use App\Models\Checkpoint;

it('admin pode listar usuarios', function () {
    $admin = createUserWithRole('admin');

    $this->actingAs($admin)
        ->getJson('/api/users')
        ->assertOk();
});

it('vigilante nao pode listar usuarios', function () {
    $vigilante = createUserWithRole('vigilante');

    $this->actingAs($vigilante)
        ->getJson('/api/users')
        ->assertForbidden();
});

it('vigilante nao pode criar checkpoint', function () {
    $vigilante = createUserWithRole('vigilante');

    $this->actingAs($vigilante)
        ->postJson('/api/checkpoints', [
            'name' => 'Portaria',
            'is_active' => true,
        ])
        ->assertForbidden();
});

it('developer pode criar checkpoint', function () {
    $developer = createUserWithRole('developer');

    $this->actingAs($developer)
        ->postJson('/api/checkpoints', [
            'name' => 'Portaria',
            'is_active' => true,
        ])
        ->assertCreated();
});