<?php

use App\Models\User;
use Spatie\Permission\Models\Role;

function createUserWithRole(string $role = 'admin', array $attributes = []): User
{
    Role::findOrCreate($role);

    $user = User::factory()->create($attributes);
    $user->assignRole($role);

    return $user;
}