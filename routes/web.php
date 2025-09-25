<?php
use Illuminate\Support\Facades\Route; 
use App\Http\Controllers\QuotationController;
use App\Http\Controllers\CotizacionesPorNombreController;
use App\Http\Controllers\CotizacionesPorReferenteController;

Route::get('/', [QuotationController::class, 'create'])->name('quotations.create');
Route::post('/quotations', [QuotationController::class, 'store'])->name('quotations.store');
Route::get('/quotations/{quotation}', [QuotationController::class, 'show'])->name('quotations.show');
Route::get('/quotations/{quotation}/pdf', [QuotationController::class, 'pdf'])->name('quotations.pdf');
// Endpoint para sugerir ítems a partir de tipos seleccionados (AJAX)
Route::get('/carpetas', [CotizacionesPorReferenteController::class, 'index'])->name('cotnom.index');
Route::get('/carpetas/{slug}', [CotizacionesPorReferenteController::class, 'show'])->name('cotnom.show');
Route::get('/carpetas/{slug}/{quotation}/pdf', [CotizacionesPorReferenteController::class, 'pdf'])->name('cotnom.pdf');
Route::get('/api/study-items', [QuotationController::class, 'studyItems'])->name('api.study-items');
Route::patch('/carpetas/{slug}/{quotation}/estado', [CotizacionesPorReferenteController::class, 'updateEstado'])->name('cotnom.updateEstado');
Route::get('/ajax/dirigidos', [QuotationController::class, 'searchDirigidos'])->name('ajax.dirigidos');
Route::get('/ajax/referentes', [QuotationController::class, 'searchReferentes'])->name('ajax.referentes');