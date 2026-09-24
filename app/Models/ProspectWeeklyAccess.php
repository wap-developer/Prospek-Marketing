<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProspectWeeklyAccess extends Model
{
    use HasFactory;

    protected $table = 'prospect_weekly_access';

    protected $fillable = ['prospect_id', 'opened_by', 'month', 'week_of_month', 'is_open', 'opened_at'];

    protected function casts(): array
    {
        return [
            'is_open' => 'boolean',
            'opened_at' => 'datetime',
            'month' => 'integer',
            'week_of_month' => 'integer',
        ];
    }

    public function prospect(): BelongsTo
    {
        return $this->belongsTo(Prospect::class);
    }

    public function openedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'opened_by');
    }
}
