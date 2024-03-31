<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;

class Task extends Model
{
    use HasFactory;

    protected $fillable = ['uuid', 'description', 'task_type', 'payload', 'status', 'result','pingback_url'];

    protected $casts = [
        'payload' => 'array',
        'result' => 'array',
    ];

    // Automatically generate a UUID for each new task
    protected static function boot()
    {
        parent::boot();
        static::creating(function ($task) {
            if (empty($task->uuid)) {
                $task->uuid = (string) Str::uuid();

            }
        });
        
        // on update make a get request to the pingback_url
        static::updated(function ($task) {
            if (!empty($task->pingback_url) && $task->status == 'completed') {
                Http::get($task->pingback_url);
            }
        });
    }
}
