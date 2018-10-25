<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TotalController extends Controller
{
    public function breakdown()
    {
        $temperatureAndHumidityBreakdown = DB::table('temperatureAndHumidity')->select(DB::raw('year(date)year, avg(humidity)humidity, avg(temperature)temperature'))->groupby(DB::raw('year(date)'))->get();
        $temperatureArray = array();
        $humidityArray = array();
        $rainfallArray = array();
        $breakdownArray = array();
        foreach ($temperatureAndHumidityBreakdown as $data)
        {
            $temperatureArray[$data->year] = $data->temperature;
            $humidityArray[$data->year] =  $data->humidity;
        }
        $rainfallByYear = DB::table('rainfall_by_year')->get();
        foreach ($rainfallByYear as $data)
        {
            $rainfallArray[$data->year] = $data->rainfall;
        }
        return ['humidity' => $humidityArray, 'temperature' => $temperatureArray, 'rainfall' => $rainfallArray];
    }



}
