<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\Models\Concerns\HasActivity;
use Spatie\Activitylog\Support\LogOptions;

class RoundLog extends Model
{
    use HasActivity;

    protected $fillable = [
        'user_id',
        'checkpoint_id',
        'latitude',
        'longitude',
        'notes',
        'scanned_at',
    ];

    protected $casts = [
        'scanned_at' => 'datetime',
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['user_id', 'checkpoint_id', 'scanned_at'])
            ->logOnlyDirty()
            ->setDescriptionForEvent(fn(string $eventName) => "Registro de ronda {$eventName}");
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function checkpoint(): BelongsTo
    {
        return $this->belongsTo(Checkpoint::class);
    }
}