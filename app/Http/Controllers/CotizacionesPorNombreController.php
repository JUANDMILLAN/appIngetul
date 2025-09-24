<?php
// app/Http/Controllers/CotizacionesPorNombreController.php

namespace App\Http\Controllers;

use App\Models\Quotation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Barryvdh\DomPDF\Facade\Pdf;

class CotizacionesPorNombreController extends Controller
{
    /** Lista de clientes (distinct por dirigido_a) con conteo de cotizaciones */
    public function index(Request $request)
{
    $perPage = 12; // ← cámbialo a 8, 10, 12, etc.
    $q = trim((string) $request->get('q', ''));

    $builder = \App\Models\Quotation::query()
        ->selectRaw('TRIM(LOWER(dirigido_a)) as key_name, MAX(dirigido_a) as display_name, COUNT(*) as total')
        ->when($q !== '', function ($qq) use ($q) {
            // Filtra antes del group by para que funcione el LIKE
            $qq->where('dirigido_a', 'LIKE', "%{$q}%");
        })
        ->groupBy('key_name')
        ->orderBy('display_name');

    // Paginamos y añadimos "slug" a cada item
    $clientes = $builder->paginate($perPage);
    $clientes->getCollection()->transform(function ($row) {
        $row->slug = Str::slug($row->display_name ?: 'sin-nombre');
        return $row;
    });

    return view('cotnom.index', compact('clientes'));
}

    /** Muestra las cotizaciones de un cliente por su nombre (dirigido_a) */
    public function show(string $slug)
    {
        // Recuperamos el nombre original usando el slug
        // Buscamos cualquier cotización cuyo slug(dirigido_a) coincida
        $quotations = Quotation::query()
            ->get()
            ->filter(function ($q) use ($slug) {
                return Str::slug((string)$q->dirigido_a ?: 'sin-nombre') === $slug;
            })
            ->sortByDesc('fecha')
            ->values();

        if ($quotations->isEmpty()) {
            abort(404);
        }

        $displayName = $quotations->first()->dirigido_a ?: 'Sin nombre';
        return view('cotnom.show', compact('displayName', 'slug', 'quotations'));
    }

    /** Genera/retorna el PDF guardado en carpeta del cliente (por dirigido_a) */
    public function pdf(string $slug, Quotation $quotation)
    {
        // Validar que la cotización pertenezca a este "cliente por nombre"
        $expected = Str::slug((string)$quotation->dirigido_a ?: 'sin-nombre');
        abort_unless($expected === $slug, 404);

        [$dir, $file] = $this->pathFor($quotation);
        $path = "{$dir}/{$file}";

        if (!Storage::disk('local')->exists($path)) {
            $pdf = Pdf::loadView('quotations.pdf', ['quotation' => $quotation->load('items')])->setPaper('letter');
            Storage::disk('local')->makeDirectory($dir);
            Storage::disk('local')->put($path, $pdf->output());
        }

        return Storage::download($path);
    }

    /** Calcula carpeta y nombre de archivo para una cotización dada */
    private function pathFor(Quotation $q): array
    {
        $slug = Str::slug((string)$q->dirigido_a ?: 'sin-nombre');
        $dir  = "cotizaciones-clientes/{$slug}";
        // Usa consecutivo si existe, si no, cae al id
        $num  = $q->consecutivo ?? $q->id;
        $prefix = $q->consecutivo ? 'COT-' : 'COT-ID-';
        $file = $prefix . str_pad((int)$num, 6, '0', STR_PAD_LEFT) . '.pdf';
        return [$dir, $file];
    }
    public function updateEstado(string $slug, \App\Models\Quotation $quotation, \Illuminate\Http\Request $request)
{
    // 1) Validar que la cotización pertenece a este slug (dirigido_a)
    $expected = \Illuminate\Support\Str::slug((string)$quotation->dirigido_a ?: 'sin-nombre');
    abort_unless($expected === $slug, 404);

    // 2) Validar el nuevo estado
    $data = $request->validate([
        'estado' => ['required','in:pendiente,aceptada,cancelada'],
    ]);

    // 3) Actualizar
    $quotation->update(['estado' => $data['estado']]);

    // 4) Volver a la carpeta con mensaje
    return back()->with('ok', 'Estado actualizado a: '.ucfirst($data['estado']));
}
}
