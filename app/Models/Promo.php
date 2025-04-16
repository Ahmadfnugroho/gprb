<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Promo extends Model
{
    use LogsActivity;

    protected $fillable = [
        'name',
        'nominal',
        'type',
        'rules',
        'active',
    ];

    protected $casts = [
        'rules' => 'array', // Untuk menyimpan aturan diskon dalam JSON
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly([
                'name',
                'nominal',
                'type',
                'rules',
                'active',
            ]);
    }

    public function calculateDiscountedDays(int $duration, string $dayOfWeek = null): int
    {
        if ($this->type === 'day_based') {
            // Aturan diskon berbasis hari (contoh: sewa 2 hari bayar 1 hari)
            $groupSize = $this->rules['group_size'] ?? 1; // Default 1 hari
            $payDays = $this->rules['pay_days'] ?? $groupSize; // Default bayar penuh

            $discountedDays = (int) ($duration / $groupSize) * $payDays;
            $remainingDays = $duration % $groupSize;

            return $discountedDays + $remainingDays; // Hari yang dibayar total
        }

        if ($this->type === 'percentage') {
            // Diskon persentase tidak mempengaruhi jumlah hari yang dibayar
            return $duration;
        }

        // Jika tidak ada aturan diskon
        return $duration;
    }

    /**
     * Hitung total diskon persentase.
     */
    public function calculatePercentageDiscount(float $total, string $dayOfWeek = null): float
    {
        if ($this->type === 'percentage') {
            $percentage = $this->rules['percentage'] ?? 0; // Default 0%
            $applicableDays = $this->rules['days'] ?? []; // Hari berlaku diskon

            // Terapkan diskon hanya jika hari berlaku atau hari kosong (semua hari berlaku)
            if (empty($applicableDays) || ($dayOfWeek && in_array($dayOfWeek, $applicableDays))) {
                return $total * ($percentage / 100);
            }
        }

        return 0; // Tidak ada diskon persentase
    }



    public function Transactions(): HasMany
    {
        return $this->hasMany(Transaction::class, 'user_id', 'id');
    }
}
