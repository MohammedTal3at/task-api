<?php

namespace App\Models;

use App\Enums\TaskLogOperationType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $task_id
 * @property int $user_id
 * @property array $changes
 * @property TaskLogOperationType $operation_type
 * @property Carbon $created_at
 * @property-read Task $task
 * @property-read User $user
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
        'operation_type' => TaskLogOperationType::class,
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
