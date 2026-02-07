<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\Auditable;

class PaymentCorrection extends Model
{
    use HasFactory, Auditable;

    protected $fillable = [
        'payment_id',
        'requested_by',
        'reviewed_by',
        'type',
        'reason',
        'old_values',
        'new_values',
        'status',
        'rejection_reason',
        'reviewed_at',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
        'reviewed_at' => 'datetime',
    ];

    /**
     * Get the payment being corrected
     */
    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class);
    }

    /**
     * Get the user who requested the correction
     */
    public function requester(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    /**
     * Get the user who reviewed the correction
     */
    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    /**
     * Check if correction is pending
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Check if correction is approved
     */
    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    /**
     * Check if correction is rejected
     */
    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }

    /**
     * Scope for pending corrections
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Get status label in Indonesian
     */
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'pending' => 'Menunggu Approval',
            'approved' => 'Disetujui',
            'rejected' => 'Ditolak',
            default => $this->status,
        };
    }

    /**
     * Get type label in Indonesian
     */
    public function getTypeLabelAttribute(): string
    {
        return match ($this->type) {
            'void' => 'Pembatalan',
            'edit' => 'Perubahan',
            default => $this->type,
        };
    }
}
