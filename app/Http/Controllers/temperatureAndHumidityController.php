<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class temperatureAndHumidityController extends Controller
{

    public function temperatureAndHumidity(Request $request)
    {
        $year = $request->year;
        $month = $request->month;
        $date = $request->date;

        if (isset($year) && ($request->type == 'humidity'))
        {
            return $this->yearlyAverageHumidity($request);
        }
        elseif(isset($year) && ($request->type == 'temperature'))
        {
            return $this->yearAverageTemperature($request);
        }

        else
        {
            return ['result' => 'false', 'data' => 'Provided condition is incorrect'];
        }
    }

    private function yearlyAverageHumidity (Request $request)
    {
        $checkIfDataExists = DB::table('temperatureAndHumidity')
            ->whereYear('date', $request->year)->exists();

        if($checkIfDataExists != true)
        {
            return ['result' => 'false', 'data' => 'The required data was not found'];
        }

        $yearlyAverageHumidity = DB::table('temperatureAndHumidity')
            ->whereYear('date', $request->year)
            ->groupBy(DB::raw("year(date)"))
            ->avg("humidity");
        return ['result' => 'true', 'Yearly average humidity' => $yearlyAverageHumidity];
    }

    private function yearAverageTemperature (Request $request)
    {
        $checkIfDataExists = DB::table('temperatureAndHumidity')
            ->whereYear('date', $request->year)->exists();

        if($checkIfDataExists != true)
        {
            return ['result' => 'false', 'data' => 'The required data was not found'];
        }

        $yearAverageTemperature = DB::table('temperatureAndHumidity')
            ->whereYear('date', $request->year)
            ->groupBy(DB::raw("year(date)"))
            ->avg("temperature");
        return ['result' => 'true', 'Yearly average temperature' => $yearAverageTemperature];
    }


}


