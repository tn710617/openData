<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class temperatureController extends Controller
{
    //


    public function temperature(Request $request)
    {
        $year = $request->year;
        $month = $request->month;
        $date = $request->date;

        if (isset($year) && isset($month) && isset($date))
        {
            return $this->dateAverageTemperature($request);
        }

        elseif(isset($year) && isset($month) && !isset($date))
        {
            return $this->monthAverageTemperature($request);
        }

        elseif (isset($year) && !isset($month) && !isset($date))
        {
            return $this->yearAverageTemperature($request);
        }

        else
        {
            return ['result' => 'false', 'memo' => 'Provided condition is not enough'];
        }
    }
    private function monthAverageTemperature (Request $request)
    {
        $checkIfDataExists = DB::table('temperatureAndHumidity')
            ->whereYear('date', $request->year)
            ->whereMonth('date', $request->month)
            ->exists();

        if($checkIfDataExists != true)
        {
            return ['result' => 'false', 'memo' => 'The queried data doesn\'t exist'];
        }

        $monthAverageTemperature = DB::table('temperatureAndHumidity')
            ->whereYear('date', $request->year)
            ->whereMonth('date', $request->month)
            ->groupBy(DB::raw("year(date)"))
            ->groupBy(DB::raw("month(date)"))
            ->avg("temperature");
        return ['result' => 'true', 'Monthly average temperature' => $monthAverageTemperature];
    }

    private function dateAverageTemperature (Request $request)

    {
        $checkIfDataExists = DB::table('temperatureAndHumidity')
            ->whereYear('date', $request->year)
            ->whereMonth('date', $request->month)
            ->whereDay('date', $request->date)
            ->exists();

        if($checkIfDataExists != true)
        {
            return ['result' => 'false', 'memo' => 'The queried data doesn\'t exist'];
        }

        $dateAverageTemperature = DB::table('temperatureAndHumidity')
            ->whereYear('date', $request->year)
            ->whereMonth('date', $request->month)
            ->whereDay('date', $request->date)
            ->groupBy(DB::raw("year(date)"))
            ->groupBy(DB::raw("month(date)"))
            ->groupBy(DB::raw("day(date)"))
            ->avg("temperature");
        return ['result' => 'true', 'Daily average temperature' => $dateAverageTemperature];
    }

    //
}
