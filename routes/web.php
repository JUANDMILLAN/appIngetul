<?php
use Illuminate\Support\Facades\Route; 
use App\Http\Controllers\QuotationController;

Route::get('/', [QuotationController::class, 'create'])->name('quotations.create');
Route::post('/quotations', [QuotationController::class, 'store'])->name('quotations.store');
Route::get('/quotations/{quotation}', [QuotationController::class, 'show'])->name('quotations.show');
Route::get('/quotations/{quotation}/pdf', [QuotationController::class, 'pdf'])->name('quotations.pdf');

// Endpoint para sugerir ítems a partir de tipos seleccionados (AJAX)
Route::get('/api/study-items', [QuotationController::class, 'studyItems'])->name('api.study-items');
