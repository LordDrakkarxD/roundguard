<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;


class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Developer -> id 1
        // Administrador -> id 2
        // Vigilante -> id 3

        $dev = User::firstOrCreate([
            'name' => 'Dev',
            'username' => 'dev',
            'cpf' => '00000000000',
            'email' => 'dev@gmail.com',
            'phone' => '00000000000',
            'birth_date' => '2023-10-24',
            'password' => Hash::make('123'),
        ]);
        $dev->assignRole('developer');

        $admin = User::firstOrCreate([
            'name' => 'ADM',
            'username' => 'adm',
            'cpf' => '00000000001',
            'email' => 'adm@gmail.com',
            'phone' => '00000000000',
            'birth_date' => '2023-10-24',
            'password' => Hash::make('123'),
        ]);
        $admin->assignRole('admin');

        $vigilante = User::firstOrCreate([
            'name' => 'Teste',
            'username' => 'teste',
            'cpf' => '00000000002',
            'email' => 'teste@gmail.com',
            'phone' => '00000000000',
            'birth_date' => '2023-10-24',
            'password' => Hash::make('123'),
        ]);
        $vigilante->assignRole('vigilante');
    }
}