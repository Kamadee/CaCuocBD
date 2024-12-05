<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthenticationController;
use App\Http\Controllers\MatchController;
use App\Http\Controllers\CustomerController;

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
})->name('dashboard')->middleware('auth');

Route::group(['prefix' => 'authentication', 'as' => 'authentication', 'middleware' => ['guest']], function(){
    Route::get('/login',[AuthenticationController::class, 'getLogin'])->name('.login');
    Route::post('/post-login',[AuthenticationController::class, 'postLogin'])->name('.postLogin');
    
    Route::get('/register',[AuthenticationController::class, 'getRegister'])->name('.register');
    Route::post('/post-register',[AuthenticationController::class, 'postRegister'])->name('.postRegister');
    Route::post('/logOut',[AuthenticationController::class, 'logOut'])->name('.logOut')->withoutMiddleware(['guest']);

});

Route::group(['prefix' => 'match', 'as' => 'match', 'middleware' => ['auth','admin']], function(){
    Route::get('/formCreateMatch',[MatchController::class, 'createFormMatch'])->name('.formCreateMatch');
    Route::post('/postCreateMatch',[MatchController::class, 'postCreateMatch'])->name('.postCreateMatch');

    Route::get('/list', [MatchController::class, 'getMatchList'])->name('.list');

    Route::post('/postEditMatch/{matchId}', [MatchController::class, 'postEditMatch'])->name('.postEditMatch');
    Route::get('/{matchId}', [MatchController::class, 'getMatchDetail'])->name('.detail');

    Route::post('/delete', [MatchController::class, 'getMatchDelete'])->name('.delete');
    Route::get('/{matchId}/userBettingList', [MatchController::class, 'getUserBettingList'])->name('.userBettingList');
    Route::get('/userAccountList', [MatchController::class, 'getUserAccountList'])->name('.userAccountList');

});

Route::group(['prefix' => 'customer', 'as' => 'customer', 'middleware' => ['auth']], function(){
    Route::get('/listMatch', [CustomerController::class, 'getListMatch'])->name('.listMatch');
    Route::get('/myHistoryBetting', [CustomerController::class, 'getHistoryBetting'])->name('.myHistoryBetting');
    Route::post('/{matchId}/betting', [CustomerController::class, 'getBetting'])->name('.betting');
    Route::get('/myAccount', [CustomerController::class, 'getMyAccount'])->name('.myAccount');
    Route::get('/search', [CustomerController::class, 'search'])->name('.search');

});
