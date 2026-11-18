<?php

use App\Http\Controllers\DirectoryController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\InspectionController;
use App\Http\Controllers\PropertyController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/about', [HomeController::class, 'about'])->name('about');
Route::get('/contact', [HomeController::class, 'contact'])->name('contact');
Route::post('/contact', [HomeController::class, 'submitContact'])->name('contact.submit');

Route::prefix('properties')->name('properties.')->group(function () {
    Route::get('/', [PropertyController::class, 'index'])->name('index');
    Route::get('/{property:slug}', [PropertyController::class, 'show'])->name('show');
    Route::post('/', [PropertyController::class, 'store'])->middleware('auth')->name('store');
});

Route::get('/agents', [DirectoryController::class, 'agents'])->name('agents.index');
Route::get('/companies', [DirectoryController::class, 'companies'])->name('companies.index');
Route::get('/professionals', [DirectoryController::class, 'professionals'])->name('professionals.index');
Route::get('/professionals/{professional:slug}', [DirectoryController::class, 'showProfessional'])->name('professionals.show');


// Route::get('/agents', [DirectoryController::class, 'agents'])->name('agents.index');
// Route::get('/companies', [DirectoryController::class, 'companies'])->name('companies.index');
// Route::get('/professionals', [DirectoryController::class, 'professionals'])->name('professionals.index');

Route::post('/favorites/toggle', [FavoriteController::class, 'toggle'])
    ->middleware('auth')
    ->name('favorites.toggle');

Route::post('/inspections', [InspectionController::class, 'store'])->name('inspections.store');

Route::get('/sitemap.xml', [HomeController::class, 'sitemap'])->name('sitemap');



require __DIR__."/auth.php";
