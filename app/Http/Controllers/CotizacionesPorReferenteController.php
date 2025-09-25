<?php

namespace App\Http\Controllers;

use App\Models\Quotation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Barryvdh\DomPDF\Facade\Pdf;

class CotizacionesPorReferenteController extends Controller
{
    // Lista de carpetas (agrupadas por referente)
    public function index(Request $request)
{
    $perPage = 12; // o 8/10/12 según prefieras
    $q = trim((string) $request->get('q',''));

    $builder = Quotation::query()
        ->when($q !== '', fn($qq) => $qq->where('referente','LIKE',"%{$q}%"))
        ->whereNotNull('referente')
        ->where('referente','<>','')
        ->selectRaw('TRIM(LOWER(referente)) as key_name, MAX(referente) as display_name, COUNT(*) as total')
        ->groupBy('key_name')
        ->orderBy('display_name');

    // Paginar y conservar query string
    $clientes = $builder->paginate($perPage)->appends($request->query());

    // Agregar el slug a cada ítem
    $clientes->getCollection()->transform(function($row){
        $row->slug = \Illuminate\Support\Str::slug($row->display_name ?: 'sin-referente');
        return $row;
    });

    return view('cotnom.index', compact('clientes'));
}

    // Listado de cotizaciones dentro de una carpeta (referente)
    public function show(string $slug)
    {
        $quotations = Quotation::query()
            ->whereNotNull('referente')
            ->get()
            ->filter(fn($q) => Str::slug((string)$q->referente ?: 'sin-referente') === $slug)
            ->sortByDesc('fecha')
            ->values();

        if ($quotations->isEmpty()) abort(404);

        $displayName = $quotations->first()->referente ?: 'Sin referente';
        return view('cotnom.show', compact('displayName','slug','quotations'));
    }

    // Descargar/Generar PDF desde la carpeta por referente
    public function pdf(string $slug, Quotation $quotation)
    {
        $expected = Str::slug((string)$quotation->referente ?: 'sin-referente');
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

    // Cambiar estado (pendiente/aceptada/cancelada)
    public function updateEstado(string $slug, Quotation $quotation, Request $request)
    {
        $expected = Str::slug((string)$quotation->referente ?: 'sin-referente');
        abort_unless($expected === $slug, 404);

        $data = $request->validate([
            'estado' => ['required','in:pendiente,aceptada,cancelada']
        ]);

        $quotation->update(['estado' => $data['estado']]);

        return back()->with('ok', 'Estado actualizado a: '.ucfirst($data['estado']));
    }

    private function pathFor(Quotation $q): array
    {
        $slug = Str::slug((string)$q->referente ?: 'sin-referente');
        $dir  = "cotizaciones-clientes/{$slug}";
        $num  = $q->consecutivo ?? $q->id;
        $prefix = $q->consecutivo ? 'COT-' : 'COT-ID-';
        $file = $prefix . str_pad((int)$num, 6, '0', STR_PAD_LEFT) . '.pdf';
        return [$dir, $file];
    }
}
