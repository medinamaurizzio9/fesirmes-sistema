<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AffiliateCredential extends Model
{
    protected $fillable = [
        'affiliate_id',
        'qr_payload',
        'qr_version',
        'regenerated_by',
        'regenerated_at',
    ];

    protected function casts(): array
    {
        return [
            'regenerated_at' => 'datetime',
        ];
    }

    public function affiliate(): BelongsTo
    {
        return $this->belongsTo(Affiliate::class);
    }

    public function regeneratedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'regenerated_by');
    }
}
