<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AuditLog extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'action',
        'model_type',
        'model_id',
        'old_values',
        'new_values',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
        'created_at' => 'datetime',
    ];

    /**
     * Get the user who performed the action
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the audited model
     */
    public function auditable()
    {
        return $this->morphTo('model');
    }

    /**
     * Log a create action
     */
    public static function logCreate(Model $model): self
    {
        return self::createLog($model, 'create', null, $model->toArray());
    }

    /**
     * Log an update action
     */
    public static function logUpdate(Model $model, array $oldValues): self
    {
        $changedFields = array_intersect_key($model->getChanges(), $oldValues);
        $oldChanged = array_intersect_key($oldValues, $changedFields);

        return self::createLog($model, 'update', $oldChanged, $changedFields);
    }

    /**
     * Log a delete action
     */
    public static function logDelete(Model $model): self
    {
        return self::createLog($model, 'delete', $model->toArray(), null);
    }

    /**
     * Create the audit log entry
     */
    protected static function createLog(Model $model, string $action, ?array $oldValues, ?array $newValues): self
    {
        return self::create([
            'user_id' => auth()->id(),
            'action' => $action,
            'model_type' => get_class($model),
            'model_id' => $model->getKey(),
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }

    /**
     * Get human-readable action name
     */
    public function getActionLabelAttribute(): string
    {
        return match ($this->action) {
            'create' => 'Dibuat',
            'update' => 'Diperbarui',
            'delete' => 'Dihapus',
            default => $this->action,
        };
    }

    /**
     * Get model short name
     */
    public function getModelNameAttribute(): string
    {
        return class_basename($this->model_type);
    }
}
