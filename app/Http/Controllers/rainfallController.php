<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class rainfallController extends Controller {

    public function rainfall(Request $request)
    {
        $year = $request->year;
        $month = $request->month;
        $district = $request->district;
        if (isset($year) && isset($month) && isset($district))
        {
            return $this->dailyDistrictSumRainfall($request);
        } elseif (isset($year) && isset($district) && !isset($month))
        {
            return $this->monthlyDistrictSumRainfall($request);
        } elseif (isset($year) && !isset($month) && !isset($district))
        {
            return $this->yearSumRainfall($request);
        } else
        {
            return ["result" => "false", "data" => "Provided condition is incorrect"];
        }
    }


    private function dailyDistrictSumRainfall(Request $request)
    {
        $this->validate(request(), [
            'year'     => 'required',
            'month'    => 'required',
            'district' => 'required'
        ]);

//        $checkIfDataExists = DB::table('rainfall')
//            ->whereYear('date', $request->year)
//            ->whereMonth('date', $request->month)
//            ->where('district', '=', $request->district)
//            ->exists();
//
//        if ($checkIfDataExists == false)
//        {
//            return ['result' => 'false', 'data' => 'The required data was not found'];
//        }
        $dailyDistrictSumRainfall = DB::table('rainfall')
            ->select(DB::raw('day(date) date, sum(rainfall)rainfall'))
            ->whereYear('date', $request->year)
            ->whereMonth('date', $request->month)
            ->where('district', '=', $request->district)
            ->groupBy(DB::raw("year(date), month(date), day(date)"))
            ->get()->toArray();

        $toBeShownAbsentData = 'Required data was not found';
        $confirmedMissingData = 0;

        $now = Carbon::now();
        $currentMonth = $now->month;
        $currentYear = $now->year;
        $currentDay = $now->day;

        $finalOutput = array();
        $howManyDaysInAMonth = cal_days_in_month(CAL_GREGORIAN, $request->month, $request->year);

        for ($daysInAMonth = 1; $daysInAMonth < $howManyDaysInAMonth; $daysInAMonth ++)
        {
            foreach ($dailyDistrictSumRainfall as $data)
            {
                if ($data->date == $daysInAMonth)
                {
                    $finalOutput[$daysInAMonth] = $data->rainfall;
                }
            }
            if (!isset($finalOutput[$daysInAMonth]))
            {
                $finalOutput[$daysInAMonth] = (($request->year < 2015) ||
                    (($request->year == 2015) && ($request->month < 9)) ||
                    (($request->year == $currentYear) && ($request->month > $currentMonth)) ||
                    ($request->year > $currentYear) ||
                    ((($request->month == $currentMonth) && ($request->year == $currentYear) && ($daysInAMonth > $currentDay)))) ? $toBeShownAbsentData : $confirmedMissingData;
            }
        }


        return ['result' => 'true', 'data' => $finalOutput];
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
            return ['result' => 'false', 'data' => 'The required data was not found'];
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
//            return ['result' => 'false', 'data' => 'The required data was not found'];
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

        for ($monthsInAYear = 1; $monthsInAYear < 13; $monthsInAYear ++)
        {
            foreach ($monthlyDistrictSumRainfall as $data)
            {
                if ($monthsInAYear == $data->month)
                {
                    $finalOutput[$monthsInAYear] = $data->rainfall;
                }
            }
            if (!isset($finalOutput[$monthsInAYear]))
            {
                $finalOutput[$monthsInAYear] = (($request->year == 2015) && ($monthsInAYear > $currentMonth)) || ($request->year > $currentYear) ||
                ((($monthsInAYear > $currentMonth) && ($request->year == $currentYear))) ? $toBeShownAbsentData : $confirmedMissingData;
            }
        }

        return ['result' => 'true', 'data' => $finalOutput];
    }


}
