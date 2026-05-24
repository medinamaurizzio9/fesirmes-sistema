<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Attendance extends Model
{
    protected $fillable = [
        'activity_id',
        'affiliate_id',
        'ci_detectado',
        'estado',
        'observacion',
        'imported_by',
        'imported_at',
        'source_file_name',
        'import_batch_id',
        'reverted_by',
        'reverted_at',
    ];

    protected function casts(): array
    {
        return [
            'imported_at' => 'datetime',
            'reverted_at' => 'datetime',
        ];
    }

    public function activity(): BelongsTo
    {
        return $this->belongsTo(Activity::class);
    }

    public function affiliate(): BelongsTo
    {
        return $this->belongsTo(Affiliate::class);
    }

    public function importedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'imported_by');
    }
}
