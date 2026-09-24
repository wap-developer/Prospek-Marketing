<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TodoLockSetting extends Model
{
    protected $fillable = [
        'is_locked',
        'auto_locked_week',
        'locked_at',
        'unlocked_at',
        'updated_by_user_id',
        'reason',
    ];

    protected function casts(): array
    {
        return [
            'is_locked' => 'boolean',
            'locked_at' => 'datetime',
            'unlocked_at' => 'datetime',
        ];
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by_user_id');
    }

    public static function instance(): self
    {
        $setting = self::firstOrCreate(['id' => 1], [
            'is_locked' => false,
            'reason' => 'To Do harian sedang direkap oleh Manager Marketing',
        ]);

        $setting->checkAndApplyMondayAutoLock();

        return $setting;
    }

    /**
     * Otomatis kunci jika sudah masuk Senin 00:00 di minggu baru
     */
    public function checkAndApplyMondayAutoLock(): void
    {
        $now = Carbon::now('Asia/Jakarta');
        $currentWeekKey = $now->format('o-\WW'); // e.g. "2026-W38"

        // Cek jika sudah hari Senin jam 00:00 atau lebih dan belum tercatat auto-lock di minggu ini
        if ($this->auto_locked_week !== $currentWeekKey) {
            // Jika hari ini Senin (atau lebih) di minggu ini, picu auto-lock minggu baru
            $this->update([
                'is_locked' => true,
                'auto_locked_week' => $currentWeekKey,
                'locked_at' => $now,
                'reason' => 'To Do harian otomatis dikunci setiap hari Senin 00:00 untuk rekap mingguan.',
            ]);
        }
    }

    public function lock(?int $userId = null, ?string $reason = null): void
    {
        $now = Carbon::now('Asia/Jakarta');
        $this->update([
            'is_locked' => true,
            'locked_at' => $now,
            'updated_by_user_id' => $userId,
            'reason' => $reason ?: 'To Do harian dikunci untuk rekap oleh Manager Marketing',
        ]);
    }

    public function unlock(?int $userId = null): void
    {
        $now = Carbon::now('Asia/Jakarta');
        $currentWeekKey = $now->format('o-\WW');
        $this->update([
            'is_locked' => false,
            'auto_locked_week' => $currentWeekKey,
            'unlocked_at' => $now,
            'updated_by_user_id' => $userId,
        ]);
    }
}
