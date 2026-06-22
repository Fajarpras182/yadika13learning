<?php

namespace App\Traits;

use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Model;

trait Auditable
{
    public static function bootAuditable()
    {
        static::created(function (Model $model) {
            $model->logAudit('created', null, $model->getAttributes());
        });

        static::updated(function (Model $model) {
            $model->logAudit('updated', $model->getOriginal(), $model->getChanges());
        });

        static::deleted(function (Model $model) {
            $model->logAudit('deleted', $model->getAttributes(), null);
        });
    }

    public function logAudit($action, $oldValues = null, $newValues = null)
    {
        if (app()->runningInConsole() && !app()->runningUnitTests()) {
            return;
        }

        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => $action,
            'auditable_type' => get_class($this),
            'auditable_id' => $this->id,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }
}
