<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Quotation;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;

class CotizacionesClienteController extends Controller
{
    /** Lista clientes con el # de cotizaciones */
    public function index()
    {
        // Ajusta si tu relación es diferente
        $clientes = User::query()
            ->select('users.*')
            ->withCount('quotations')
            ->whereHas('quotations')
            ->orderBy('name')
            ->get();

        return view('cotcli.index', compact('clientes'));
    }

    /** Muestra las cotizaciones de un cliente */
    public function show(User $user)
    {
        $quotations = Quotation::where('user_id', $user->id)
            ->with('items') 
            ->orderByDesc('fecha')
            ->get();

        return view('cotcli.show', compact('user', 'quotations'));
    }

    /** Genera/retorna el PDF guardado en carpeta de cliente */
    public function pdf(User $user, Quotation $q)
    {
        abort_unless($q->user_id === $user->id, 404);

        $slug = Str::slug($user->name ?: ('cliente-'.$user->id));
        $fileName = $this->fileName($q->consecutivo);
        $dir = "cotizaciones-clientes/{$slug}";
        $path = "{$dir}/{$fileName}";

        if (!Storage::disk('local')->exists($path)) {
            // Genera PDF y guarda
            $pdf = Pdf::loadView('quotations.pdf', ['quotation' => $q->load('items')])
                      ->setPaper('letter');
            Storage::disk('local')->makeDirectory($dir);
            Storage::disk('local')->put($path, $pdf->output());
        }

        // Sirve descarga (o usa response()->file si quieres inline)
        return Storage::download($path);
    }

    private function fileName(int $consecutivo): string
    {
        return 'COT-'.str_pad($consecutivo, 6, '0', STR_PAD_LEFT).'.pdf';
    }
}
