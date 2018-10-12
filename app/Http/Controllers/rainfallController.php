<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class rainfallController extends Controller
{

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
            return $this->monthlyDistrictSumRainfall($request);
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
            return ["result" => "false", "memo" => "Provided condition is incorrect"];
        }
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
            return ['result' => 'false', 'memo' => 'The queried data doesn\'t exists'];
        }
        $monthDistrictSumRainfall = DB::table('rainfall')
            ->whereYear('date', $request->year)
            ->whereMonth('date', $request->month)
            ->where('district', '=', $request->district)
            ->groupBy(DB::raw("year(date)", "month(date)"))
            ->sum('rainfall');

        return ['result' => 'true', 'monthlyDistrictSumRainfall' => $monthDistrictSumRainfall];
    }

    private function monthlyDistrictSumRainfall(Request $request)
    {
        $this->validate(request(), [
            'year'     => 'required',
            'district' => 'required'
        ]);

//        $checkIfDataExists = DB::table('rainfall')
//            ->whereYear('date', $request->year)
//            ->where('district', '=', $request->district)
//            ->exists();
//
//        if ($checkIfDataExists == false)
//        {
//            return ['result' => 'false', 'memo' => 'The queried data doesn\'t exists'];
//        }
        $monthlyDistrictSumRainfall = DB::table('rainfall')
            ->select(DB::raw('month(date) month, sum(rainfall) rainfall'))
            ->whereYear('date', $request->year)
            ->where('district', $request->district)
            ->groupBy(DB::raw('year(date), month(date)'))
            ->get()->toArray();


        $toBeShownAbsentData = 'Required data was not found';
        $confirmedMissingData = 0;

        $now = Carbon::now();
        $currentMonth = $now->month;
        $currentYear = $now->year;
        $currentDay = $now->day;

        $finalOutput = array();

        for($monthsInAYear = 1; $monthsInAYear < 13; $monthsInAYear++)
        {
            foreach($monthlyDistrictSumRainfall as $data)
            {
                if($monthsInAYear == $data->month)
                {
                    $finalOutput[$monthsInAYear] = $data->rainfall;
                }
            }
            if(!isset($finalOutput[$monthsInAYear]))
            {
                $finalOutput[$monthsInAYear] = (($request->year == 2015) && ($monthsInAYear > $currentMonth) ) || ($request->year > $currentYear) ||
                ((($monthsInAYear > $currentMonth) && ($request->year == $currentYear))) ? $toBeShownAbsentData : $confirmedMissingData;
            }
        }

        return ['result' => 'true', 'data' => $finalOutput];
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
            return ['result' => 'false', 'memo' => 'The queried data doesn\'t exists'];
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


        return ['result' => 'true', 'monthlySumRainfall' => $monthSumRainfall];
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
            return ['result' => 'false', 'memo' => 'The queried data doesn\'t exists'];
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

        $yearlySumRainfall = $yearSumRainfallForAllOfTheDistricts / $districtNumberInDesignatedYear;


        return ['result' => 'true', 'data' => $yearlySumRainfall];
    }


}
