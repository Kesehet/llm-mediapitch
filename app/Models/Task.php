<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class Task extends Model
{
    use HasFactory;

    protected $fillable = ['uuid', 'description', 'task_type', 'payload', 'status', 'result'];

    protected $casts = [
        'payload' => 'array',
        'result' => 'array',
    ];

    // Automatically generate a UUID for each new task
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($task) {
            // Check if task already has a UUID (in case it's manually set)
            if (empty($task->uuid)) {
                $task->uuid = (string) Str::uuid();

            }

            if (!empty($task->task_type) && !empty($task->payload)) {
                $hashInput = $task->task_type . serialize($task->payload);
                $task->description = Hash::make($hashInput);
            }


        });
    }
}
