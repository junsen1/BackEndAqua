<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\WaterParamsController;
use App\Http\Controllers\TodosController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});


Route::get('/todos/mine', [TodosController::class, 'byUserId'])->middleware(['auth'])->name('mine');
Route::get('/todos/{id}/edit', [TodosController::class, 'edit'])->middleware(['auth'])->name('edit-form');
Route::get('/todos/create', [TodosController::class, 'create'])->middleware(['auth'])->name('create-form');
Route::post('/todos/{id}/update', [TodosController::class, 'update'])->middleware(['auth'])->name('update');
Route::post('/todos/add', [TodosController::class, 'store'])->middleware(['auth'])->name('add');
Route::get('/todos/{id}', [TodosController::class, 'show'])->middleware(['auth'])->name('show');
Route::delete('/todos/:id', [TodosController::class, 'delete'])->middleware(['auth'])->name('delete');

Route::get('/dashboard', [TodosController::class, 'index'])->middleware(['auth'])->name('dashboard');

Route::get('/params/mine', [WaterParamsController::class, 'byUserId'])->middleware(['auth'])->name('channels');;



require __DIR__.'/auth.php';
