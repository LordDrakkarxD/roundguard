<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Checkpoint;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use tbQuar\Facades\Quar;

use App\Http\Requests\StoreCheckpointRequest;
use App\Http\Requests\UpdateCheckpointRequest;

class CheckpointController extends Controller
{
    public function index()
    {
        $checkpoints = Checkpoint::latest()->get();

        return response()->json($checkpoints);
    }

    public function store(StoreCheckpointRequest $request)
    {
        $data = $request->validated();

        // Gera um código único para o QR
        $data['code'] = strtoupper(Str::random(10));

        $checkpoint = Checkpoint::create($data);

        return response()->json($checkpoint, 201);
    }

    public function show(Checkpoint $checkpoint)
    {
        return response()->json($checkpoint);
    }

    public function update(UpdateCheckpointRequest $request, Checkpoint $checkpoint)
    {
        $checkpoint->update($request->validated());

        return response()->json($checkpoint);
    }

    public function destroy(Checkpoint $checkpoint)
    {
        $checkpoint->delete();

        return response()->json(null, 204);
    }

    public function qrcode(Checkpoint $checkpoint)
    {
        $qr = Quar::size(200)
            ->generate($checkpoint->code);

        return response($qr)->header('Content-Type', 'image/svg+xml');
    }

    public function print(Checkpoint $checkpoint)
    {
        $qr = \tbQuar\Facades\Quar::size(400)->generate($checkpoint->code);

        return view('checkpoints.print', [
            'checkpoint' => $checkpoint,
            'qr' => $qr,
        ]);
    }
}