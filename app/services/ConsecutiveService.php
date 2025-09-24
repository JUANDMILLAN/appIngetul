<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class ConsecutiveService
{
    /**
     * Devuelve el próximo consecutivo para la clave dada (p.ej. 'quotations')
     * de forma atómica (segura en concurrencia).
     */
    public function next(string $key): int
    {
        return DB::transaction(function () use ($key) {
            $row = DB::table('counters')->where('key', $key)->lockForUpdate()->first();
            if (!$row) {
                DB::table('counters')->insert([
                    'key' => $key, 'value' => 0,
                    'created_at' => now(), 'updated_at' => now()
                ]);
                $row = (object)['value' => 0];
            }
            $next = $row->value + 1;
            DB::table('counters')->where('key', $key)->update(['value' => $next, 'updated_at' => now()]);
            return $next;
        });
    }

    /** Formatea: COT-000001 (por si lo quieres usar en vistas) */
    public static function format(int $n, int $pad = 6): string
    {
        return 'COT-'.str_pad($n, $pad, '0', STR_PAD_LEFT);
    }
}
