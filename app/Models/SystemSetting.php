<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class SystemSetting extends Model
{
    public const DEFAULTS = [
        'institution_name' => 'FESIRMES LA PAZ',
        'institution_acronym' => 'FESIRMES',
        'institution_subtitle' => 'Federación Sindical de Ramas Médicas de Salud Pública',
        'institution_address' => '',
        'institution_phones' => '',
        'institution_email' => '',
        'institution_website' => '',
        'pdf_footer' => 'Sistema Institucional FESIRMES',
    ];

    protected $fillable = [
        'key',
        'value',
    ];

    public static function getValue(string $key, ?string $default = null): ?string
    {
        return static::query()->where('key', $key)->value('value') ?? $default ?? self::DEFAULTS[$key] ?? null;
    }

    public static function setValue(string $key, ?string $value): void
    {
        static::query()->updateOrCreate(['key' => $key], ['value' => $value]);
    }

    public static function institutional(): array
    {
        return collect(self::DEFAULTS)
            ->mapWithKeys(fn ($default, $key) => [$key => self::getValue($key, $default)])
            ->all() + [
                'system_logo_path' => self::logoPath(),
                'system_logo_url' => self::logoUrl(),
            ];
    }

    public static function logoPath(): ?string
    {
        $path = self::getValue('system_logo_path');

        if (! $path) {
            return null;
        }

        $path = ltrim($path, '/');
        $path = preg_replace('#^(storage|public)/#', '', $path);

        return $path ?: null;
    }

    public static function hasLogo(): bool
    {
        $path = self::logoPath();

        return filled($path) && Storage::disk('public')->exists($path);
    }

    public static function logoUrl(): ?string
    {
        if (! self::hasLogo()) {
            return null;
        }

        return Storage::disk('public')->url(self::logoPath());
    }

    public static function logoDataUri(): ?string
    {
        if (! self::hasLogo()) {
            return null;
        }

        $absolutePath = Storage::disk('public')->path(self::logoPath());
        $mime = mime_content_type($absolutePath) ?: 'image/png';

        return 'data:'.$mime.';base64,'.base64_encode(file_get_contents($absolutePath));
    }
}
