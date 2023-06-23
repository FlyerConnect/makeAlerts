<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MakeTest;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Route::get('/scenarios', [MakeTest::class, 'getAllScenarios']);
Route::get('/pingScenarios', [MakeTest::class, 'pingScenarios']);
Route::get('mail/send-grid', [MakeTest::class, 'sendMail']);

Route::get('/', function () {
    return view('welcome');
});
