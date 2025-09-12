<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $task_id
 * @property int $user_id
 * @property array $changes
 * @property string $operation_type
 * @property \Illuminate\Support\Carbon $created_at
 * @property-read \App\Models\Task $task
 * @property-read \App\Models\User $user
 */
class TaskLog extends Model
{
    public $timestamps = false;

    protected $table = 'task_log';

    protected $fillable = [
        'task_id',
        'user_id',
        'changes',
        'operation_type',
        'created_at',
    ];

    protected $casts = [
        'changes' => 'array',
        'created_at' => 'datetime',
    ];

    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}