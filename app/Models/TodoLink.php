<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TodoLink extends Model
{
    use HasFactory;

    protected $fillable = ['todo_id', 'platform', 'slot', 'url'];

    public function todo(): BelongsTo
    {
        return $this->belongsTo(Todo::class);
    }
}
