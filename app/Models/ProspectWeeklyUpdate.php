<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProspectWeeklyUpdate extends Model
{
    use HasFactory;

    protected $touches = ['prospect'];

    protected $fillable = ['prospect_id', 'user_id', 'year', 'iso_week', 'month', 'week_of_month', 'note', 'progress'];

    public function prospect(): BelongsTo
    {
        return $this->belongsTo(Prospect::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
