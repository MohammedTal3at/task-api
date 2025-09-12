<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @property int $id
 * @property string $name
 * @property string|null $color
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Task[] $tasks
 */
class Tag extends Model
{
    protected $fillable = [
        'name',
        'color',
    ];

    public function tasks(): BelongsToMany
    {
        return $this->belongsToMany(Task::class, 'tag_task');
    }
}
