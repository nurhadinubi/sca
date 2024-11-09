<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Scaleup\ScaleUpController;
use App\Http\Controllers\User\UserController;
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


Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');


Route::middleware('auth')->group(function () {

    Route::get('/', function () {
        return view('layouts.template');
    });

    Route::get('/getMaterial', [ScaleUpController::class, 'getMaterial'])->name('api.getMaterial');
    Route::get('/getMaterialById', [ScaleUpController::class, 'getMaterialById'])->name('api.getMaterialById');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/user', [UserController::class, 'index'])->name('user.index');
    Route::get('/user/create', [UserController::class, 'create'])->name('user.create');

    // Scale Up
    Route::get('/scaleup', [ScaleUpController::class, 'index'])->name('scaleup.index');
    Route::get('/scaleup/create', [ScaleUpController::class, 'create'])->name('scaleup.create');
    Route::post('/scaleup/item', [ScaleUpController::class, 'saveItem'])->name('scaleup.saveItem');
    Route::post('/scaleup/item/delete', [ScaleUpController::class, 'deleteItem'])->name('scaleup.deleteItem');
    Route::post('/scaleup/store', [ScaleUpController::class, 'store'])->name('scaleup.store');
    Route::get('/scaleup/{id}', [ScaleUpController::class, 'show'])->name('scaleup.show');
    Route::get('/scaleup/approve/{id}', [ScaleUpController::class, 'approve'])->name('scaleup.approve');
    Route::post('/scaleup/approvestore/{id}', [ScaleUpController::class, 'approvestore'])->name('scaleup.approvestore');
    Route::get('/scaleup/print/{id}', [ScaleUpController::class, 'print'])->name('scaleup.print');
});

require __DIR__ . '/auth.php';
