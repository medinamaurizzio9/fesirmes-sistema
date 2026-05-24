<?php

namespace App\Services;

use App\Models\Activity;
use App\Models\Affiliate;
use App\Models\Attendance;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class AttendanceCsvImporter
{
    public function import(Activity $activity, UploadedFile $file, ?string $selectedColumn = null): array
    {
        $batchId = (string) Str::uuid();
        $sourceName = $file->getClientOriginalName();
        $rows = $this->readRows($file->getRealPath());
        $header = $this->header($rows);
        $ciIndex = $this->detectCiIndex($header, $selectedColumn);
        $summary = [
            'batch_id' => $batchId,
            'source_file_name' => $sourceName,
            'total' => 0,
            'validos' => 0,
            'duplicados' => 0,
            'observados' => 0,
            'invalidos' => 0,
            'errores' => [],
            'headers' => $header,
        ];

        $seen = [];
        $dataRows = $this->dataRows($rows, $header);

        foreach ($dataRows as $rowNumber => $row) {
            $summary['total']++;
            $rawCi = $row[$ciIndex] ?? '';
            $ci = $this->cleanCi($rawCi);

            if ($ci === '') {
                $this->store($activity, null, '', 'invalido', 'Fila sin C.I.', $batchId, $sourceName);
                $summary['invalidos']++;
                $summary['errores'][] = ['ci' => '(vacio)', 'observacion' => 'Fila sin C.I.'];
                continue;
            }

            $affiliate = Affiliate::where('ci', $ci)->first();
            $alreadyValid = $affiliate && Attendance::where('activity_id', $activity->id)
                ->where('affiliate_id', $affiliate->id)
                ->where('estado', 'valido')
                ->whereNull('reverted_at')
                ->exists();

            if (isset($seen[$ci]) || $alreadyValid) {
                $this->store($activity, $affiliate, $ci, 'duplicado', 'C.I. repetido en la actividad.', $batchId, $sourceName);
                $summary['duplicados']++;
                $summary['errores'][] = ['ci' => $ci, 'observacion' => 'Duplicado'];
                continue;
            }

            $seen[$ci] = true;

            if (! $affiliate) {
                $this->store($activity, null, $ci, 'invalido', 'No existe afiliado con este C.I.', $batchId, $sourceName);
                $summary['invalidos']++;
                $summary['errores'][] = ['ci' => $ci, 'observacion' => 'No existe afiliado'];
                continue;
            }

            if ($affiliate->status->value !== 'activo') {
                $this->store($activity, $affiliate, $ci, 'observado', 'Afiliado con estado '.$affiliate->status->value.'.', $batchId, $sourceName);
                $summary['observados']++;
                $summary['errores'][] = ['ci' => $ci, 'observacion' => 'Afiliado '.$affiliate->status->value];
                continue;
            }

            $this->store($activity, $affiliate, $ci, 'valido', 'Asistencia valida.', $batchId, $sourceName);
            $summary['validos']++;
        }

        return $summary;
    }

    public function previewHeaders(UploadedFile $file): array
    {
        return $this->header($this->readRows($file->getRealPath()));
    }

    private function readRows(string $path): array
    {
        $handle = fopen($path, 'rb');
        $rows = [];

        if ($handle === false) {
            return $rows;
        }

        while (($row = fgetcsv($handle)) !== false) {
            $rows[] = array_map(fn ($value) => trim((string) $value), $row);
        }

        fclose($handle);

        return $rows;
    }

    private function header(array $rows): array
    {
        $first = $rows[0] ?? ['ci'];
        $looksLikeHeader = collect($first)->contains(fn ($cell) => preg_match('/[a-zA-Z]/', $cell));

        return $looksLikeHeader ? $first : ['ci'];
    }

    private function dataRows(array $rows, array $header): array
    {
        if (($rows[0] ?? null) === $header) {
            return array_slice($rows, 1, null, true);
        }

        return $rows;
    }

    private function detectCiIndex(array $header, ?string $selectedColumn): int
    {
        if ($selectedColumn !== null && $selectedColumn !== '') {
            $index = array_search($selectedColumn, $header, true);
            if ($index !== false) {
                return (int) $index;
            }
        }

        foreach ($header as $index => $name) {
            if (Str::lower($name) === 'ci' || str_contains(Str::lower($name), 'c.i')) {
                return (int) $index;
            }
        }

        return 0;
    }

    private function cleanCi(string $ci): string
    {
        $ci = preg_replace('/^\xEF\xBB\xBF/', '', trim($ci)) ?? '';

        return preg_replace('/[^0-9A-Za-z]/', '', $ci) ?? '';
    }

    private function store(Activity $activity, ?Affiliate $affiliate, string $ci, string $estado, string $observacion, string $batchId, string $sourceName): void
    {
        Attendance::create([
            'activity_id' => $activity->id,
            'affiliate_id' => $affiliate?->id,
            'ci_detectado' => $ci,
            'estado' => $estado,
            'observacion' => $observacion,
            'imported_by' => Auth::id(),
            'imported_at' => now(),
            'source_file_name' => $sourceName,
            'import_batch_id' => $batchId,
        ]);
    }
}
