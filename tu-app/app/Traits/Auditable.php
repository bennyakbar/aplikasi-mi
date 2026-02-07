<?php

namespace App\Traits;

use App\Models\AuditLog;

trait Auditable
{
    /**
     * Boot the trait
     */
    public static function bootAuditable(): void
    {
        // Log when a model is created
        static::created(function ($model) {
            AuditLog::logCreate($model);
        });

        // Log when a model is updated
        static::updating(function ($model) {
            // Store original values before update
            $model->auditOriginal = $model->getOriginal();
        });

        static::updated(function ($model) {
            if (isset($model->auditOriginal)) {
                AuditLog::logUpdate($model, $model->auditOriginal);
                unset($model->auditOriginal);
            }
        });

        // Log when a model is deleted
        static::deleted(function ($model) {
            AuditLog::logDelete($model);
        });
    }

    /**
     * Get the audit logs for this model
     */
    public function auditLogs()
    {
        return AuditLog::where('model_type', get_class($this))
            ->where('model_id', $this->getKey())
            ->orderBy('created_at', 'desc')
            ->get();
    }
}
