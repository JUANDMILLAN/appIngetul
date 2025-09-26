<?php

return [
    'required' => 'Este campo es obligatorio.',
    'attributes' => [
        'fecha'        => 'Fecha',
        'ciudad'       => 'Ciudad',
        'departamento' => 'Departamento',
        'dirigido_a'   => 'Dirigido a',
        'referente'    => 'Referente',
        'objeto'       => 'Objeto',
        'items'                      => 'Ítems',
        'items.*.descripcion'        => 'Descripción del ítem',
        'items.*.und'                => 'Unidad',
        'items.*.cantidad'           => 'Cantidad',
        'items.*.vr_unitario'        => 'Valor unitario',
        'items.*.vr_total'           => 'Valor total',
    ],
];
