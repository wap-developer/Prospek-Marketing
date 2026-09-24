<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TodoPdf extends Model
{
    use HasFactory;

    protected $fillable = ['todo_id', 'task', 'file_path', 'original_name'];

    public function todo(): BelongsTo
    {
        return $this->belongsTo(Todo::class);
    }
}
