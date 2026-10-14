<?php

use App\Http\Controllers\PagoController;
use App\Http\Controllers\PeliculaController;
use App\Http\Controllers\ReservaController;
use App\Models\Pelicula;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    $peliculas = Pelicula::all();
    return view('welcome', compact('peliculas'));
})->name('home');

Route::get('/peliculas/{pelicula:slug}', [PeliculaController::class, 'show'])->name('peliculas.show');
Route::get('/funciones/{funcion}/butacas', [ReservaController::class, 'selectButacas'])->name('reservas.butacas');
Route::post('/reservas/procesar', [ReservaController::class, 'store'])->name('reservas.store');
Route::get('/pago', [PagoController::class, 'index'])->name('pago.index');
Route::post('/pago/procesar', [PagoController::class, 'procesar'])->name('pago.procesar');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
});

require __DIR__.'/settings.php';
