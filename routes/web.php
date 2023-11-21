<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\CardController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\AuthenticatedUserController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AuctionController;

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;

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

// Home
Route::redirect('/', '/login');

Route::get('/home', [AuthenticatedUserController::class, 'index'])->name('home');

// Authentication
Route::controller(LoginController::class)->group(function () {
    Route::get('/login', 'showLoginForm')->name('login');
    Route::post('/login', 'authenticate');
    Route::get('/logout', 'logout')->name('logout');
});

Route::controller(RegisterController::class)->group(function () {
    Route::get('/register', 'showRegistrationForm')->name('register');
    Route::post('/register', 'register');
});

Route::controller(AuthenticatedUserController::class)->group(function () {
    Route::get('/profile/{id}', 'show')->name('show');
    Route::get('/profile/{id}/edit', 'edit')->name('edit');
    Route::post('/profile/{id}/edit', 'update')->name('update');
    Route::get('/profile/{id}/balance', [AuthenticatedUserController::class, 'balance'])->name('balance');
    Route::post('/profile/{id}/balance', [AuthenticatedUserController::class, 'addFunds'])->name('addFunds');
    Route::get('/auctionCreate', [AuthenticatedUserController::class,'showCreateAuction'])-> name('showCreateAuction');
    Route::post('/auctionCreate', [AuthenticatedUserController::class,'createAuction'])-> name('createAuction');

});


Route::controller(AuctionController::class)->group(function () {
    Route::get('/auctions/{pageNr}', [AuctionController::class, 'index'])->name('auction.index');

    Route::get('/auction/{id}', [AuctionController::class, 'show'])->name('auction.show');
});


