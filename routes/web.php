<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

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

Route::get('/online', function (Request $request) {
    $stations = $request->input('stations');
    $show_offline_stations = $request->input('show_offline_stations', 'true');

    if ($show_offline_stations == 'true') {
        $show_offline_stations = 'true';
    } else {
        $show_offline_stations = 'false';
    }

    if($stations == null or $stations == '') {
        abort(403);
    }

    $stations = explode(',', $stations);
    for($i = 0; $i < count($stations); $i++) {
        $stations[$i] = trim($stations[$i]);
    }

    $stations = json_encode($stations);
    
    return view('welcome', ['stations' => $stations, 'show_offline_stations' => $show_offline_stations]);
});
