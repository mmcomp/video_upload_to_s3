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

/*
 * Retrieve the video file here
 */
Route::get('/', 'VideoController@create');
/*
 * Store the video file to the s3 storage
 * and Generate 4 frames of the video and
 * store it to the public folder
 */
Route::post('/', 'VideoController@store');
