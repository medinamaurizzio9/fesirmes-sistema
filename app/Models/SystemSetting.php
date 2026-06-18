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
        return self::normalizeStoragePath(self::getValue('system_logo_path'));
    }

    public static function hasLogo(): bool
    {
        $path = self::logoPath();

        return filled($path) && (
            Storage::disk('public')->exists($path)
            || Storage::disk('local')->exists($path)
        );
    }

    public static function logoUrl(): ?string
    {
        $path = self::logoPath();

        if (! $path) {
            return null;
        }

        if (Storage::disk('public')->exists($path)) {
            return Storage::disk('public')->url($path).'?v='.Storage::disk('public')->lastModified($path);
        }

        if (Storage::disk('local')->exists($path)) {
            return self::dataUriFromAbsolutePath(Storage::disk('local')->path($path), 'image/png');
        }

        return null;
    }

    public static function logoDataUri(): ?string
    {
        $path = self::logoAbsolutePath();

        if (! $path) {
            return null;
        }

        return self::dataUriFromAbsolutePath($path, 'image/png');
    }

    public static function logoAbsolutePath(): ?string
    {
        $path = self::logoPath();

        if (! $path) {
            return null;
        }

        if (Storage::disk('public')->exists($path)) {
            return Storage::disk('public')->path($path);
        }

        if (Storage::disk('local')->exists($path)) {
            return Storage::disk('local')->path($path);
        }

        return null;
    }

    public static function normalizeStoragePath(?string $path): ?string
    {
        if (! $path) {
            return null;
        }

        $path = str_replace('\\', '/', ltrim($path, '/'));
        $path = preg_replace('#^(storage/app/(public|private)|app/(public|private)|storage|public|private)/#', '', $path);

        return $path ?: null;
    }

    private static function dataUriFromAbsolutePath(string $path, string $fallbackMime): string
    {
        $mime = mime_content_type($path) ?: $fallbackMime;

        return 'data:'.$mime.';base64,'.base64_encode(file_get_contents($path));
    }
}
