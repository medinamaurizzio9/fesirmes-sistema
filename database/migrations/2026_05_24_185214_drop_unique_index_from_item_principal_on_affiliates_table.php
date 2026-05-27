<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        try {
            DB::statement('ALTER TABLE affiliates DROP INDEX affiliates_item_principal_unique');
        } catch (QueryException $exception) {
            if (($exception->errorInfo[1] ?? null) !== 1091) {
                throw $exception;
            }
        }
    }

    public function down(): void
    {
        try {
            DB::statement('ALTER TABLE affiliates ADD UNIQUE affiliates_item_principal_unique (item_principal)');
        } catch (QueryException $exception) {
            if (! in_array($exception->errorInfo[1] ?? null, [1061, 1062], true)) {
                throw $exception;
            }
        }
    }
};
