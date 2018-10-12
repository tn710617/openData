<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class humidityController extends Controller
{

    private function dailyAverageHumidity (Request $request)
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

        $dailyAverageHumidity = DB::table('temperatureAndHumidity')
            ->whereYear('date', $request->year)
            ->whereMonth('date', $request->month)
            ->whereDay('date', $request->date)
            ->groupBy(DB::raw("year(date)"))
            ->groupBy(DB::raw("month(date)"))
            ->groupBy(DB::raw("day(date)"))
            ->avg("humidity");
        return ['result' => 'true', 'Daily average humidity' => $dailyAverageHumidity];
    }

    private function monthlyAverageHumidity (Request $request)
    {
        $checkIfDataExists = DB::table('temperatureAndHumidity')
            ->whereYear('date', $request->year)
            ->whereMonth('date', $request->month)
            ->exists();

        if($checkIfDataExists != true)
        {
            return ['result' => 'false', 'memo' => 'The queried data doesn\'t exist'];
        }

        $monthlyAverageHumidity = DB::table('temperatureAndHumidity')
            ->whereYear('date', $request->year)
            ->whereMonth('date', $request->month)
            ->groupBy(DB::raw("year(date)"))
            ->groupBy(DB::raw("month(date)"))
            ->avg("humidity");
        return ['result' => 'true', 'Monthly average humidity' => $monthlyAverageHumidity];
    }

    private function yearlyAverageHumidity (Request $request)
    {
        $checkIfDataExists = DB::table('temperatureAndHumidity')
            ->whereYear('date', $request->year)->exists();

        if($checkIfDataExists != true)
        {
            return ['result' => 'false', 'memo' => 'The queried data doesn\'t exist'];
        }

        $yearlyAverageHumidity = DB::table('temperatureAndHumidity')
            ->whereYear('date', $request->year)
            ->groupBy(DB::raw("year(date)"))
            ->avg("humidity");
        return ['result' => 'true', 'Yearly average humidity' => $yearlyAverageHumidity];
    }


    public function humidity(Request $request)
    {
        $year = $request->year;
        $month = $request->month;
        $date = $request->date;

        if (isset($year) && isset($month) && isset($date))
        {
            return $this->dailyAverageHumidity($request);
        }

        elseif(isset($year) && isset($month) && !isset($date))
        {
            return $this->monthlyAverageHumidity($request);
        }

        elseif (isset($year) && !isset($month) && !isset($date))
        {
            return $this->yearlyAverageHumidity($request);
        }

        else
        {
            return ['result' => 'false', 'memo' => 'Provided condition is not enough'];
        }
    }
}
