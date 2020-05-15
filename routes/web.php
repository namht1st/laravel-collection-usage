<?php

use Illuminate\Support\Facades\Route;

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

Route::get('/', function () {
    return view('welcome');
});

Route::get('/pricing',"DemoController@pricingLampsAndWallet");
Route::get('/binary-to-decimal',"DemoController@binaryToDecimal");
Route::get('/ranking-kcoin',"DemoController@rankingKcoin");
Route::get('/compare-revenue',"DemoController@compareMonthlyRevenue");
Route::get('/github-score',"CollectionController@githubScore");
