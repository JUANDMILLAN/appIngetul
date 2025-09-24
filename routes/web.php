<?php
use Illuminate\Support\Facades\Route; 
use App\Http\Controllers\QuotationController;
use App\Http\Controllers\CotizacionesPorNombreController;

Route::get('/', [QuotationController::class, 'create'])->name('quotations.create');
Route::post('/quotations', [QuotationController::class, 'store'])->name('quotations.store');
Route::get('/quotations/{quotation}', [QuotationController::class, 'show'])->name('quotations.show');
Route::get('/quotations/{quotation}/pdf', [QuotationController::class, 'pdf'])->name('quotations.pdf');
// Endpoint para sugerir ítems a partir de tipos seleccionados (AJAX)
Route::get('/carpetas', [CotizacionesPorNombreController::class, 'index'])->name('cotnom.index');
Route::get('/carpetas/{slug}', [CotizacionesPorNombreController::class, 'show'])->name('cotnom.show');
Route::get('/carpetas/{slug}/{quotation}/pdf', [CotizacionesPorNombreController::class, 'pdf'])->name('cotnom.pdf');
Route::get('/api/study-items', [QuotationController::class, 'studyItems'])->name('api.study-items');
Route::patch('/carpetas/{slug}/{quotation}/estado', [CotizacionesPorNombreController::class, 'updateEstado'])->name('cotnom.updateEstado');