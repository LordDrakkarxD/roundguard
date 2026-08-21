<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Checkpoint;
use App\Models\RoundLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Http\Requests\StoreRoundLogRequest;

class RoundLogController extends Controller
{
    public function index(Request $request)
    {
        $query = RoundLog::with(['user:id,name', 'checkpoint:id,name,code'])
            ->latest('scanned_at');

        $user = $request->user();

        // Agente só vê os próprios registros
        if ($user->hasRole('agente')) {
            $query->where('user_id', $user->id);
        } else {
            // Admin / Supervisor podem filtrar
            if ($request->filled('user_id')) {
                $query->where('user_id', $request->user_id);
            }
        }

        if ($request->filled('checkpoint_id')) {
            $query->where('checkpoint_id', $request->checkpoint_id);
        }

        if ($request->filled('month')) {
            $query->whereMonth('scanned_at', $request->month);
        }

        if ($request->filled('year')) {
            $query->whereYear('scanned_at', $request->year);
        }

        $logs = $query->paginate(20);

        return response()->json($logs);
    }

    public function store(StoreRoundLogRequest $request)
    {
        $data = $request->validated();

        // Busca o checkpoint pelo código do QR
        $checkpoint = Checkpoint::where('code', $data['code'])
            ->where('is_active', true)
            ->first();

        if (!$checkpoint) {
            return response()->json([
                'message' => 'QR Code inválido ou ponto inativo.',
            ], 422);
        }

        $log = RoundLog::create([
            'user_id' => Auth::id(),
            'checkpoint_id' => $checkpoint->id,
            'latitude' => $data['latitude'] ?? null,
            'longitude' => $data['longitude'] ?? null,
            'notes' => $data['notes'] ?? null,
            'scanned_at' => now(),
        ]);

        $log->load(['user:id,name', 'checkpoint:id,name,code']);

        return response()->json($log, 201);
    }

    public function destroy(RoundLog $roundLog)
    {
        $user = auth()->user();

        if (!$user->hasRole('admin') && !$user->hasRole('developer')) {
            return response()->json(['message' => 'Não autorizado.'], 403);
        }

        $roundLog->delete();

        return response()->json(null, 204);
    }

    public function show(RoundLog $roundLog)
    {
        $roundLog->load(['user:id,name', 'checkpoint:id,name,code']);

        return response()->json($roundLog);
    }
}