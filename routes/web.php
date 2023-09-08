<?php

use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\ProfileController;
use App\Models\Item;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

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

//Get the domain in the env file app_url
$url = $string = preg_replace('/.+:\/\//', '', config('app.url'));
$domain = preg_replace('/:.+/', '',  $url);

Route::get('/', function (Request $request) {
    return view('welcome', ['tenant' => explode('.', $request->getHost())[0]]);
});

Route::get('register', [RegisteredUserController::class, 'create'])
    ->name('register');

Route::post('register', [RegisteredUserController::class, 'store']);

Route::middleware('tenant')->domain('{tenant}.' . $domain)->group(function () {
    require __DIR__ . '/auth.php';

    Route::get('/dashboard', function ($tenant) {
        $items = Item::where('tenant_id', auth()->user()->tenant_id)->get();
        return view('dashboard', ['items' => $items]);
    })->middleware(['auth', 'verified'])->name('dashboard');

    Route::middleware('auth')->group(function () {
        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
        Route::post('items', [ItemController::class, 'store'])->name('items.store');
    });
});
