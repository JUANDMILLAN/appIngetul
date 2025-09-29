<?php
use Illuminate\Support\Facades\Route; 
use App\Http\Controllers\QuotationController;
use App\Http\Controllers\CotizacionesPorNombreController;
use App\Http\Controllers\CotizacionesPorReferenteController;
use Illuminate\Support\Facades\Auth;
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
// Home y Profile apuntando a la carpeta /views/quotations
Route::view('/home', 'quotations.home')->name('home');
Route::view('/profile', 'quotations.profile')->name('profile');
// Vistas simples (pueden estar vacías por ahora)
Route::view('/login', 'auth.login')->name('login');
// PROYECTOS (placeholders ya OK)
Route::prefix('proyectos')->name('proyectos.')->group(function () {
    Route::get('suelos',      fn () => view('placeholders.suelos'))->name('suelos.index');
    Route::get('calculo',     fn () => view('placeholders.calculo'))->name('calculo.index');
    Route::get('planos',      fn () => view('placeholders.planos'))->name('planos.index');
    Route::get('terminados',  fn () => view('placeholders.terminados'))->name('terminados.index');
});
// Laboratorio / Reportes / Usuarios (placeholders simples por ahora)
Route::get('laboratorio', fn () => 'laboratorio')->name('laboratorio.index');
Route::get('reportes',    fn () => 'reportes')->name('reportes.index');
Route::get('users',       fn () => 'users')->name('users.index');
// routes/web.php
Route::get('quotations/{quotation}/edit', [QuotationController::class, 'edit'])->name('quotations.edit');
Route::put('quotations/{quotation}',        [QuotationController::class, 'update'])->name('quotations.update');


