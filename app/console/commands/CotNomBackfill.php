<?php
// app/Console/Commands/CotNomBackfill.php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Quotation;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Barryvdh\DomPDF\Facade\Pdf;

class CotNomBackfill extends Command
{
    protected $signature = 'cotnom:backfill {--rewrite}';
    protected $description = 'Genera PDFs organizados por nombre (dirigido_a)';

    public function handle(): int
    {
        $rewrite = (bool)$this->option('rewrite');
        $this->info('Generando PDFs por cliente (dirigido_a)...');

        Quotation::with('items')->chunk(200, function ($chunk) use ($rewrite) {
            foreach ($chunk as $q) {
                $slug = Str::slug((string)$q->dirigido_a ?: 'sin-nombre');
                $dir  = "cotizaciones-clientes/{$slug}";
                $num  = $q->consecutivo ?? $q->id;
                $prefix = $q->consecutivo ? 'COT-' : 'COT-ID-';
                $file = $prefix . str_pad((int)$num, 6, '0', STR_PAD_LEFT) . '.pdf';
                $path = "{$dir}/{$file}";

                if ($rewrite || !Storage::disk('local')->exists($path)) {
                    $pdf = Pdf::loadView('quotations.pdf', ['quotation' => $q])->setPaper('letter');
                    Storage::disk('local')->makeDirectory($dir);
                    Storage::disk('local')->put($path, $pdf->output());
                }
                $this->line("OK: {$path}");
            }
        });

        $this->info('Listo.');
        return self::SUCCESS;
    }
}
