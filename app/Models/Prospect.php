<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Prospect extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_phone',
        'service_id',
        'entry_date',
        'entry_time',
        'sender_id',
        'group_id',
        'source_id',
        'marketing_user_id',
        'status_id',
        'nominal_closing',
        'note',
        'closed_at',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'entry_date' => 'date',
            'closed_at' => 'datetime',
            'nominal_closing' => 'decimal:2',
        ];
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function sender(): BelongsTo
    {
        return $this->belongsTo(Sender::class);
    }

    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class);
    }

    public function source(): BelongsTo
    {
        return $this->belongsTo(ProspectSource::class, 'source_id');
    }

    public function prospectSource(): BelongsTo
    {
        return $this->source();
    }

    public function marketing(): BelongsTo
    {
        return $this->belongsTo(User::class, 'marketing_user_id');
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(ProspectStatus::class, 'status_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function weeklyUpdates(): HasMany
    {
        return $this->hasMany(ProspectWeeklyUpdate::class)->orderByDesc('month')->orderByDesc('week_of_month');
    }

    public function weeklyAccess(): HasMany
    {
        return $this->hasMany(ProspectWeeklyAccess::class);
    }
}
