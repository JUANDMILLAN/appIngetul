<?php

namespace App\Http\Controllers;

use App\Models\Quotation;
use App\Models\QuotationItem;
use App\Models\StudyType;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

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
        return view('quotations.create', compact('studyTypes', 'defaultNotes'));
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

    public function store(Request $request)
    {
        // 1) Validar encabezado + items
        $data = $request->validate([
            'fecha'        => ['required','date'],
            'ciudad'       => ['required','string'],
            'departamento' => ['required','string'],
            'dirigido_a'   => ['required','string'],
            'objeto'       => ['nullable','string'],
            'notas'        => ['nullable','array'],

            'items'                     => ['required','array','min:1'],
            'items.*.descripcion'       => ['required','string'],
            'items.*.und'               => ['required','string','max:10'],
            'items.*.cantidad'          => ['required','integer','min:1'],
            'items.*.vr_unitario'       => ['required','integer','min:0'],
        ]);

        // 2) Crear encabezado
        $quotation = Quotation::create([
            'fecha'        => $data['fecha'],
            'ciudad'       => $data['ciudad'],
            'departamento' => $data['departamento'],
            'dirigido_a'   => $data['dirigido_a'],
            'objeto'       => $data['objeto'] ?? null,
            'notas'        => $data['notas'] ?? [],
        ]);

        // 3) Crear items
        foreach ($data['items'] as $row) {
            $row['vr_total'] = (int)$row['cantidad'] * (int)$row['vr_unitario'];
            $quotation->items()->create($row);
        }

        return redirect()->route('quotations.show', $quotation);
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
}
