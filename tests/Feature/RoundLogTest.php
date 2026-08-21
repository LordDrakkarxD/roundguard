<?php

use App\Models\Checkpoint;

it('usuario autenticado pode registrar ronda com codigo valido', function () {
    $user = createUserWithRole('vigilante');
    $checkpoint = Checkpoint::create([
        'name' => 'Portaria',
        'code' => 'ABC123XYZ',
        'is_active' => true,
    ]);

    $this->actingAs($user)
        ->postJson('/api/rounds', [
            'code' => 'ABC123XYZ',
            'latitude' => -5.7793,
            'longitude' => -35.2009,
        ])
        ->assertCreated();
});

it('rejeita codigo de qr invalido', function () {
    $user = createUserWithRole('vigilante');

    $this->actingAs($user)
        ->postJson('/api/rounds', [
            'code' => 'CODIGO-INVALIDO',
        ])
        ->assertStatus(422);
});