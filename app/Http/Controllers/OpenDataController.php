<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OpenDataController extends Controller {


    public function dengue(Request $request)
    {
        $year = $request->year;
        $month = $request->month;
        $district = $request->district;
        if (isset($year) && isset($month) && isset($district))
        {
            return $this->dengueDistrictMonthNumber($request);
        }

        elseif (isset($year) && isset($month) && !isset($district))
        {
            return $this->dengueMonthNumber($request);
        }

        elseif (isset($year) && isset($district) && !isset($month))
        {
            return $this->dengueDistrictYearNumber($request);
        }

        elseif(isset($year) && (!isset($month)) && (!isset($district)))
        {
            return $this->dengueYearNumber($request);
        }

        else
        {
            return ["result" => "false", "memo" => "Provided condition is incorrect"];
        }
    }

    public function dengueFatalityRate(Request $request)
    {
        $this->validate(request(), [
            'year' => 'required',
        ]);

        $checkIfConditionExists = DB::table('fatalityRate')
            ->where('date', '=', $request->year)
            ->exists();

        if ($checkIfConditionExists == false)
        {
            return ['result' => 'false', 'memo' => 'The queried data doesn\'t existt'];
        }
        $dengueFatalityRate = DB::table('fatalityRate')
            ->where('date', '=', $request->year)
            ->first();

        return ['result' => 'true', 'number' => $dengueFatalityRate->fatalityRate];
    }

    public function rainfall(Request $request)
    {
        $year = $request->year;
        $month = $request->month;
        $district = $request->district;
        if(isset($year) && isset($month) && isset($district))
        {
            return $this->monthDistrictSumRainfall($request);
        }

        elseif(isset($year) && isset($district) && !isset($month))
        {
            return $this->yearDistrictSumRainfall($request);
        }

        elseif(isset($year) && isset($month) && !isset($district))
        {
            return $this->monthSumRainfall($request);
        }

        elseif(isset($year) && !isset($month) && !isset($district))
        {
            return $this->yearSumRainfall($request);
        }
        else
        {
            return ["result" => "false", "memo" => "Provided condition is not enough"];
        }
    }

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

    private function yearAverageTemperature (Request $request)
    {
        $checkIfDataExists = DB::table('temperatureAndHumidity')
            ->whereYear('date', $request->year)->exists();

        if($checkIfDataExists != true)
        {
            return ['result' => 'false', 'memo' => 'The queried data doesn\'t exist'];
        }

        $yearAverageTemperature = DB::table('temperatureAndHumidity')
            ->whereYear('date', $request->year)
            ->groupBy(DB::raw("year(date)"))
            ->avg("temperature");
        return ['result' => 'true', 'Yearly average temperature' => $yearAverageTemperature];
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

    private function monthDistrictSumRainfall(Request $request)
    {
        $this->validate(request(), [
            'year'     => 'required',
            'month'    => 'required',
            'district' => 'required'
        ]);

        $checkIfDataExists = DB::table('rainfall')
            ->whereYear('date', $request->year)
            ->whereMonth('date', $request->month)
            ->where('district', '=', $request->district)
            ->exists();

        if ($checkIfDataExists == false)
        {
            return ['result' => 'false', 'memo' => 'The queried data doesn\'t existt'];
        }
        $monthDistrictSumRainfall = DB::table('rainfall')
            ->whereYear('date', $request->year)
            ->whereMonth('date', $request->month)
            ->where('district', '=', $request->district)
            ->groupBy(DB::raw("year(date)", "month(date)"))
            ->sum('rainfall');

        return ['result' => 'true', 'number' => $monthDistrictSumRainfall];
    }

    private function yearDistrictSumRainfall(Request $request)
    {
        $this->validate(request(), [
            'year'     => 'required',
            'district' => 'required'
        ]);

        $checkIfDataExists = DB::table('rainfall')
            ->whereYear('date', $request->year)
            ->where('district', '=', $request->district)
            ->exists();

        if ($checkIfDataExists == false)
        {
            return ['result' => 'false', 'memo' => 'The queried data doesn\'t existt'];
        }
        $yearDistrictSumRainfall = DB::table('rainfall')
            ->whereYear('date', $request->year)
            ->where('district', '=', $request->district)
            ->groupBy(DB::raw("year(date)"))
            ->sum('rainfall');

        return ['result' => 'true', 'number' => $yearDistrictSumRainfall];
    }

    private function monthSumRainfall(Request $request)
    {
        $this->validate(request(), [
            'year'  => 'required',
            'month' => 'required',
        ]);

        $checkIfDataExists = DB::table('rainfall')
            ->whereYear('date', $request->year)
            ->whereMonth('date', $request->month)
            ->exists();

        if ($checkIfDataExists == false)
        {
            return ['result' => 'false', 'memo' => 'The queried data doesn\'t existt'];
        }
        $monthSumRainfallForAllOfTheDistricts = DB::table('rainfall')
            ->whereYear('date', $request->year)
            ->whereMonth('date', $request->month)
            ->groupBy(DB::raw("year(date)"))
            ->groupBy(DB::raw("month(date)"))
            ->sum('rainfall');
//        dd($monthSumRainfallForAllOfTheDistricts);

        $districtNumberInDesignatedMonth = DB::table('rainfall')
            ->whereYear('date', $request->year)
            ->whereMonth('date', $request->month)
            ->groupBy(DB::raw("year(date)"))
            ->groupBy(DB::raw("month(date)"))
            ->groupBy(DB::raw("day(date)"))
            ->count('district');

        $monthSumRainfall = $monthSumRainfallForAllOfTheDistricts / $districtNumberInDesignatedMonth;


        return ['result' => 'true', 'number' => $monthSumRainfall];
    }

    private function yearSumRainfall(Request $request)
    {
        $this->validate(request(), [
            'year' => 'required',
        ]);

        $checkIfDataExists = DB::table('rainfall')
            ->whereYear('date', $request->year)
            ->exists();

        if ($checkIfDataExists == false)
        {
            return ['result' => 'false', 'memo' => 'The queried data doesn\'t existt'];
        }
        $yearSumRainfallForAllOfTheDistricts = DB::table('rainfall')
            ->whereYear('date', $request->year)
            ->groupBy(DB::raw("year(date)"))
            ->sum('rainfall');

//        dd($yearSumRainfallForAllOfTheDistricts);

        $districtNumberInDesignatedYear = DB::table('rainfall')
            ->whereYear('date', $request->year)
            ->groupBy(DB::raw("year(date)"))
            ->groupBy(DB::raw("month(date)"))
            ->groupBy(DB::raw("day(date)"))
            ->count('district');

        $monthSumRainfall = $yearSumRainfallForAllOfTheDistricts / $districtNumberInDesignatedYear;


        return ['result' => 'true', 'number' => $monthSumRainfall];
    }

    private function dengueDistrictMonthNumber(Request $request)
    {
        $this->validate(request(), [
            'year'     => 'required',
            'month'    => 'required',
            'district' => 'required'
        ]);

        $checkIfDataExists = DB::table('dengue')
            ->whereYear('date', $request->year)
            ->whereMonth('date', $request->month)
            ->where('district', '=', $request->district)
            ->exists();

        if ($checkIfDataExists == false)
        {
            return ['result' => 'false', 'memo' => 'The queried data doesn\'t existt'];
        }
        $dengueDistrictMonthNumber = DB::table('dengue')
            ->whereYear('date', $request->year)
            ->whereMonth('date', $request->month)
            ->where('district', '=', $request->district)
            ->groupBy(DB::raw("year(date)", "month(date)", 'district'))
            ->count('district');

        return ['result' => 'true', 'number' => $dengueDistrictMonthNumber];
    }

    private function dengueMonthNumber(Request $request)
    {
        $this->validate(request(), [
            'year'  => 'required',
            'month' => 'required'
        ]);

        $checkIfDataExists = DB::table('dengue')
            ->whereYear('date', $request->year)
            ->whereMonth('date', $request->month)
            ->exists();

        if ($checkIfDataExists == false)
        {
            return ['result' => 'false', 'memo' => 'The queried data doesn\'t existt'];
        }
        $dengueMonthNumber = DB::table('dengue')
            ->whereYear('date', $request->year)
            ->whereMonth('date', $request->month)
            ->groupBy(DB::raw("year(date)", "month(date)"))
            ->count('district');

        return ['result' => 'true', 'monthlyDengueNumber' => $dengueMonthNumber];
    }

    private function dengueDistrictYearNumber(Request $request)
    {
        $this->validate(request(), [
            'year'     => 'required',
            'district' => 'required'
        ]);

        $checkIfDataExists = DB::table('dengue')
            ->whereYear('date', $request->year)
            ->where('district', '=', $request->district)
            ->exists();

        if ($checkIfDataExists == false)
        {
            return ['result' => 'false', 'memo' => 'The queried data doesn\'t existt'];
        }
        $dengueDistrictYearNumber = DB::table('dengue')
            ->whereYear('date', $request->year)
            ->where('district', '=', $request->district)
            ->groupBy(DB::raw("year(date)", 'district'))
            ->count('district');

        return ['result' => 'true', 'yearlyDengueDistrictNumber' => $dengueDistrictYearNumber];
    }

    private function dengueYearNumber(Request $request)
    {
        $this->validate(request(), [
            'year' => 'required',
        ]);

        $checkIfDataExists = DB::table('dengue')
            ->whereYear('date', $request->year)
            ->exists();

        if ($checkIfDataExists == false)
        {
            return ['result' => 'false', 'memo' => 'The queried data doesn\'t existt'];
        }
        $dengueYearNumber = DB::table('dengue')
            ->whereYear('date', $request->year)
            ->groupBy(DB::raw("year(date)"))
            ->count('district');

        return ['result' => 'true', 'yearlyDengueNumber' => $dengueYearNumber];
    }


}
