<?php

namespace App\Http\Controllers;

use App\Models\Quotation;
use App\Models\QuotationItem;
use App\Models\StudyType;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;        // 👈 nuevo
use Illuminate\Support\Str;
use App\Services\ConsecutiveService;
use Illuminate\Support\Facades\DB;          // 👈 nuevo

class QuotationController extends Controller
{
    public function create()
    {
        $studyTypes = StudyType::orderBy('label')->get();
        $defaultNotes = [
            'Esta cotización tiene una validez de 30 días',
            'La forma de pago será el 50% anticipo, excedente contra entrega',
            'Cuando se presenten cambios en el perfil estratigráfico se realizarán ensayos adicionales',
            'Este proyecto se factura como obra civil',
            'Se debe suministrar topografía en caso de pendientes',
        ];
        $clients = \App\Models\User::orderBy('name')->get(['id','name','email']);
        return view('quotations.create', compact('studyTypes', 'defaultNotes', 'clients'));
    }

    // devuelve plantillas de ítems a partir de keys seleccionadas
    public function studyItems(Request $request)
    {
        $keys = (array) $request->input('keys', []);
        $types = StudyType::whereIn('key', $keys)->get();

        $items = $types->map(function($t){
            return [
                'descripcion' => $t->description,
                'und' => $t->default_unit,
                'cantidad' => $t->default_qty,
                'vr_unitario' => $t->default_unit_price,
                'vr_total' => $t->default_qty * $t->default_unit_price,
            ];
        })->values();

        return response()->json($items);
    }

    public function store(Request $request, ConsecutiveService $consecutive)

    {
    // Valida TODO, incluyendo user_id SIEMPRE
    $data = $request->validate([
        'user_id'      => ['required','exists:users,id'],    // 👈 SIEMPRE
        'fecha'        => ['required','date'],
        'ciudad'       => ['required','string'],
        'departamento' => ['required','string'],
        'dirigido_a'   => ['required','string'],
        'objeto'       => ['nullable','string'],
        'notas'        => ['nullable','array'],
        'referente'    => ['nullable','string','max:150'],

        

        'items'                     => ['required','array','min:1'],
        'items.*.descripcion'       => ['required','string'],
        'items.*.und'               => ['required','string','max:10'],
        'items.*.cantidad'          => ['required','integer','min:1'],
        'items.*.vr_unitario'       => ['required','integer','min:0'],
    ]);

    $next = $consecutive->next('quotations');

    $quotation = Quotation::create([
        'user_id'      => (int) $data['user_id'],          // 👈 CLAVE
        'consecutivo'  => $next,
        'fecha'        => $data['fecha'],
        'ciudad'       => $data['ciudad'],
        'departamento' => $data['departamento'],
        'dirigido_a'   => $data['dirigido_a'],
        'objeto'       => $data['objeto'] ?? null,
        'notas'        => $data['notas'] ?? [],
        'estado'       => 'pendiente',
        'referente'    => $data['referente'] ?? null,

    ]);

    // 4) Crear items
    foreach ($data['items'] as $row) {
        $row['vr_total'] = (int)$row['cantidad'] * (int)$row['vr_unitario'];
        $quotation->items()->create($row);
    }

    // 5) Generar y guardar PDF en carpeta del cliente
    $this->ensureReferentePdf($quotation);

   $slug = \Illuminate\Support\Str::slug((string)$quotation->referente ?: 'sin-referente');
return redirect()->route('cotnom.show', $slug)->with('ok', 'Cotización creada y PDF guardado');
}


    public function show(Quotation $quotation)
    {
        $quotation->load('items');
        return view('quotations.show', compact('quotation'));
    }

    public function pdf(Quotation $quotation)
    {
        $quotation->load('items');
        $pdf = Pdf::loadView('quotations.pdf', compact('quotation'))
                  ->setPaper('letter'); // o 'a4'
        return $pdf->download('cotizacion-'.$quotation->id.'.pdf');
    }
  public function searchDirigidos(Request $request)
{
    $q = trim((string) $request->get('q', ''));

    $rows = \App\Models\Quotation::query()
        ->whereNotNull('dirigido_a')
        ->where('dirigido_a', '!=', '')
        ->when($q !== '', fn($qq) => $qq->where('dirigido_a', 'like', "%{$q}%"))
        ->selectRaw('LOWER(TRIM(dirigido_a)) as key_name, MAX(dirigido_a) as display_name')
        ->groupBy('key_name')
        ->orderBy('display_name')
        ->limit(10)
        ->get();

    return response()->json(
        $rows->map(fn ($r) => ['value' => $r->display_name, 'text' => $r->display_name])->values()
    );
}
public function searchReferentes(Request $request)
{
    $q = trim((string)$request->get('q',''));

    $rows = \App\Models\Quotation::query()
        ->whereNotNull('referente')
        ->where('referente','!=','')
        ->when($q !== '', fn($qq) => $qq->where('referente','like',"%{$q}%"))
        ->selectRaw('LOWER(TRIM(referente)) as key_name, MAX(referente) as display_name')
        ->groupBy('key_name')
        ->orderBy('display_name')
        ->limit(10)
        ->get();

    return response()->json(
        $rows->map(fn($r) => ['value' => $r->display_name, 'text' => $r->display_name])->values()
    );
}
private function ensureReferentePdf(\App\Models\Quotation $q): void
{
    $q->loadMissing('items');

    $slug = \Illuminate\Support\Str::slug($q->referente ?: 'sin-referente');
    $dir  = "cotizaciones-clientes/{$slug}";
    $file = 'COT-'.str_pad($q->consecutivo, 6, '0', STR_PAD_LEFT).'.pdf';
    $path = "{$dir}/{$file}";

    $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('quotations.pdf', ['quotation' => $q])->setPaper('letter');

    \Illuminate\Support\Facades\Storage::disk('local')->makeDirectory($dir);
    \Illuminate\Support\Facades\Storage::disk('local')->put($path, $pdf->output()); // ← sobrescribe siempre
}

public function edit(Quotation $quotation)
{
    // Cargar items y datos para selects
    $quotation->load('items');

    $studyTypes   = \App\Models\StudyType::orderBy('label')->get();
    $clients      = \App\Models\User::orderBy('name')->get(['id','name','email']);
    $defaultNotes = $quotation->notas ?? []; // o las notas por defecto si quieres precargar

    return view('quotations.edit', compact('quotation','studyTypes','clients','defaultNotes'));
}



public function update(Request $request, Quotation $quotation)
{
    $data = $request->validate([
        'user_id'      => ['required','exists:users,id'],
        'fecha'        => ['required','date'],
        'ciudad'       => ['required','string'],
        'departamento' => ['required','string'],
        'dirigido_a'   => ['required','string'],
        'referente'    => ['nullable','string','max:150'],
        'objeto'       => ['nullable','string'],
        'notas'        => ['nullable','array'],

        'items'               => ['required','array','min:1'],
        'items.*.id'          => ['nullable','integer','exists:quotation_items,id'],
        'items.*.descripcion' => ['required','string'],
        'items.*.und'         => ['required','string','max:10'],
        'items.*.cantidad'    => ['required','integer','min:1'],
        'items.*.vr_unitario' => ['required','integer','min:0'],
    ]);

    DB::transaction(function() use ($data, $quotation) {

        // 1) Actualizar cabecera
        $quotation->update([
            'user_id'      => (int)$data['user_id'],
            'fecha'        => $data['fecha'],
            'ciudad'       => $data['ciudad'],
            'departamento' => $data['departamento'],
            'dirigido_a'   => $data['dirigido_a'],
            'referente'    => $data['referente'] ?? null,
            'objeto'       => $data['objeto'] ?? null,
            'notas'        => $data['notas'] ?? [],
            // NO toques 'estado' aquí salvo que tengas un selector de estado en el form
        ]);

        // 2) Sincronizar ítems (update / create / delete)
        $enviados = collect($data['items']);

        // ids enviados (para conservarlos)
        $idsEnviados = $enviados->pluck('id')->filter()->all();

        // borrar los que ya no vienen
        $quotation->items()->whereNotIn('id', $idsEnviados ?: [0])->delete();

        // upsert simple
        foreach ($enviados as $row) {
            $payload = [
                'descripcion'  => $row['descripcion'],
                'und'          => $row['und'],
                'cantidad'     => (int) $row['cantidad'],
                'vr_unitario'  => (int) $row['vr_unitario'],
                'vr_total'     => (int) $row['cantidad'] * (int) $row['vr_unitario'],
            ];

            if (!empty($row['id'])) {
                $quotation->items()->where('id', $row['id'])->update($payload);
            } else {
                $quotation->items()->create($payload);
            }
        }
    });

    // 3) Regenerar/guardar PDF en carpeta por REFERENTE (como ya haces)
    $this->ensureReferentePdf($quotation->fresh('items'));

    // 4) Redirigir a la carpeta por referente
    $slug = \Illuminate\Support\Str::slug((string)($quotation->referente ?: 'sin-referente'));
    return redirect()->route('cotnom.show', $slug)->with('ok', 'Cotización actualizada');
}


}