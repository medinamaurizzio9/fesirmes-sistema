<?php

namespace App\Services;

use App\Models\Audit;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuditLogger
{
    public static function record(string $action, ?Model $model = null, array $oldValues = [], array $newValues = []): void
    {
        /** @var Request|null $request */
        $request = request();

        Audit::create([
            'user_id' => Auth::id(),
            'action' => $action,
            'auditable_type' => $model ? $model::class : null,
            'auditable_id' => $model?->getKey(),
            'old_values' => $oldValues ?: null,
            'new_values' => $newValues ?: null,
            'ip_address' => $request?->ip(),
            'user_agent' => $request?->userAgent(),
        ]);
    }
}
