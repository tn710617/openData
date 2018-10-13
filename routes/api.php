<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/dengue', 'dengueController@dengue');
Route::post('/dengueFatalityRate', 'OpenDataController@dengueFatalityRate');
Route::post('/rainfall', 'rainfallController@rainfall');
Route::post('/temperatureAndHumidity', 'temperatureAndHumidityController@temperatureAndHumidity');



//Route::post('/dengueMonthNumber', 'OpenDataController@dengueMonthNumber');
//Route::post('/dengueYearNumber', 'OpenDataController@dengueYearNumber');
//Route::post('/dengueDistrictYearNumber', 'OpenDataController@dengueDistrictYearNumber');
//Route::post('/dengueDistrictMonthNumber', 'OpenDataController@dengueDistrictMonthNumber');


//Route::post('/yearDistrictSumRainfall', 'OpenDataController@yearDistrictSumRainfall');
//Route::post('/monthDistrictSumRainfall', 'OpenDataController@monthDistrictSumRainfall');
//Route::post('/monthSumRainfall', 'OpenDataController@monthSumRainfall');
//Route::post('/yearSumRainfall', 'OpenDataController@yearSumRainfall');


//Route::post('/yearAverageTemperature', 'OpenDataController@yearAverageTemperature');
//Route::post('/monthAverageTemperature', 'OpenDataController@monthAverageTemperature');
//Route::post('/dateAverageTemperature', 'OpenDataController@dateAverageTemperature');
