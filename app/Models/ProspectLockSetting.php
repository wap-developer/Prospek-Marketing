<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProspectLockSetting extends Model
{
    protected $fillable = [
        'is_locked',
        'last_scheduled_slot',
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

    /**
     * Tentukan kunci slot waktu (WIB / Asia/Jakarta)
     * Malam: jam 22:00 s.d 05:59:59 (slot e.g. "2026-10-06-night")
     * Siang: jam 06:00 s.d 21:59:59 (slot e.g. "2026-10-06-day")
     */
    public static function currentSlotKey(?Carbon $now = null): string
    {
        $now = $now ? $now->copy()->setTimezone('Asia/Jakarta') : Carbon::now('Asia/Jakarta');
        $hour = (int) $now->format('H');

        if ($hour >= 22) {
            return $now->format('Y-m-d') . '-night';
        } elseif ($hour < 6) {
            return $now->copy()->subDay()->format('Y-m-d') . '-night';
        } else {
            return $now->format('Y-m-d') . '-day';
        }
    }

    public static function isNightSlot(?Carbon $now = null): bool
    {
        $now = $now ? $now->copy()->setTimezone('Asia/Jakarta') : Carbon::now('Asia/Jakarta');
        $hour = (int) $now->format('H');

        return $hour >= 22 || $hour < 6;
    }

    /**
     * Cek apakah sudah berganti slot terjadwal dan terapkan auto-lock / auto-unlock
     */
    public function checkAndApplySchedule(?Carbon $now = null): void
    {
        $now = $now ? $now->copy()->setTimezone('Asia/Jakarta') : Carbon::now('Asia/Jakarta');
        $currentSlot = self::currentSlotKey($now);

        if ($this->last_scheduled_slot !== $currentSlot) {
            $isNight = self::isNightSlot($now);

            if ($isNight) {
                $this->update([
                    'is_locked' => true,
                    'last_scheduled_slot' => $currentSlot,
                    'locked_at' => $now,
                    'reason' => 'Input prospek ditutup otomatis pada jam 22:00 - 06:00 WIB.',
                ]);
            } else {
                $this->update([
                    'is_locked' => false,
                    'last_scheduled_slot' => $currentSlot,
                    'unlocked_at' => $now,
                    'reason' => null,
                ]);
            }
        }
    }

    public static function instance(?Carbon $now = null): self
    {
        $now = $now ? $now->copy()->setTimezone('Asia/Jakarta') : Carbon::now('Asia/Jakarta');
        $isNight = self::isNightSlot($now);
        $currentSlot = self::currentSlotKey($now);

        $setting = self::first();
        if (! $setting) {
            $setting = new self();
            $setting->id = 1;
            $setting->is_locked = $isNight;
            $setting->last_scheduled_slot = $currentSlot;
            $setting->locked_at = $isNight ? $now : null;
            $setting->unlocked_at = $isNight ? null : $now;
            $setting->reason = $isNight ? 'Input prospek ditutup otomatis pada jam 22:00 - 06:00 WIB.' : null;
            $setting->save();
        }

        $setting->checkAndApplySchedule($now);

        return $setting;
    }

    public function lock(?int $userId = null, ?string $reason = null, ?Carbon $now = null): void
    {
        $now = $now ? $now->copy()->setTimezone('Asia/Jakarta') : Carbon::now('Asia/Jakarta');
        $currentSlot = self::currentSlotKey($now);

        $this->update([
            'is_locked' => true,
            'last_scheduled_slot' => $currentSlot,
            'locked_at' => $now,
            'updated_by_user_id' => $userId,
            'reason' => $reason ?: 'Input prospek dikunci manual oleh Manager/Admin.',
        ]);
    }

    public function unlock(?int $userId = null, ?Carbon $now = null): void
    {
        $now = $now ? $now->copy()->setTimezone('Asia/Jakarta') : Carbon::now('Asia/Jakarta');
        $currentSlot = self::currentSlotKey($now);

        $this->update([
            'is_locked' => false,
            'last_scheduled_slot' => $currentSlot,
            'unlocked_at' => $now,
            'updated_by_user_id' => $userId,
            'reason' => null,
        ]);
    }
}
