<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TodoExport extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'month',
        'year',
        'export_type',
        'start_date',
        'end_date',
        'period_label',
        'status',
        'total_marketing',
        'processed_marketing',
        'filename',
        'file_path',
        'error_message',
        'completed_at',
    ];

    protected $casts = [
        'month' => 'integer',
        'year' => 'integer',
        'start_date' => 'date',
        'end_date' => 'date',
        'total_marketing' => 'integer',
        'processed_marketing' => 'integer',
        'completed_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getPercentAttribute(): int
    {
        if ($this->total_marketing <= 0) {
            return $this->status === 'completed' ? 100 : 0;
        }

        return (int) min(100, round(($this->processed_marketing / $this->total_marketing) * 100));
    }
}
