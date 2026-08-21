<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\RoundLog;
use App\Models\Checkpoint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;

use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;

class UserController extends Controller
{
    public function index()
    {
        $users = User::with('roles:id,name')
            ->latest()
            ->get();

        return response()->json($users);
    }

    public function store(StoreUserRequest $request)
    {
        $data = $request->validated();

        $user = User::create([
            'name' => $data['name'],
            'username' => $data['username'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'cpf' => $data['cpf'] ?? null,
            'phone' => $data['phone'] ?? null,
            'is_active' => $data['is_active'] ?? true,
        ]);

        $user->assignRole($data['role']);
        $user->load('roles:id,name');

        return response()->json($user, 201);
    }

    public function show(User $user)
    {
        $user->load('roles:id,name');
        return response()->json($user);
    }

    public function update(UpdateUserRequest $request, User $user)
    {
        $data = $request->validated();

        $user->name = $data['name'];
        $user->username = $data['username'];
        $user->email = $data['email'];
        $user->cpf = $data['cpf'] ?? null;
        $user->phone = $data['phone'] ?? null;
        $user->is_active = $data['is_active'] ?? true;

        if (!empty($data['password'])) {
            $user->password = Hash::make($data['password']);
        }

        $user->save();

        // Atualiza a role
        $user->syncRoles([$data['role']]);
        $user->load('roles:id,name');

        return response()->json($user);
    }

    public function destroy(User $user)
    {
        // Evita que o usuário delete a si mesmo
        if ($user->id === auth()->id()) {
            return response()->json(['message' => 'Você não pode excluir a si mesmo.'], 422);
        }

        $user->delete();

        return response()->json(null, 204);
    }

    public function roles()
    {
        $roles = Role::select('id', 'name')->get();
        return response()->json($roles);
    }

    public function stats()
    {
        $today = now()->startOfDay();

        return response()->json([
            'checkpoints' => Checkpoint::where('is_active', true)->count(),
            'rounds_today' => RoundLog::whereDate('scanned_at', $today)->count(),
            'active_agents' => User::role('vigilante')->where('is_active', true)->count(),
            'total_users' => User::where('is_active', true)->count(),
        ]);
    }
}