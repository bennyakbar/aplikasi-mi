<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaymentItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'payment_id',
        'fee_id',
        'period_month',
        'period_year',
        'amount',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class);
    }

    public function fee(): BelongsTo
    {
        return $this->belongsTo(Fee::class);
    }

    /**
     * Get formatted period
     */
    public function getFormattedPeriodAttribute(): string
    {
        if ($this->period_month && $this->period_year) {
            $months = [
                1 => 'Januari',
                2 => 'Februari',
                3 => 'Maret',
                4 => 'April',
                5 => 'Mei',
                6 => 'Juni',
                7 => 'Juli',
                8 => 'Agustus',
                9 => 'September',
                10 => 'Oktober',
                11 => 'November',
                12 => 'Desember'
            ];
            return $months[$this->period_month] . ' ' . $this->period_year;
        }
        return '-';
    }
}
