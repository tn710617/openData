<?php
public function dengue(Request $request)
{
    $year = $request->year;
    $month = $request->month;
    $district = $request->district;
    if (isset($year) && isset($month) && isset($district))
    {
        return $this->dengueDistrictMonthNumber($request);
    } elseif (isset($year) && isset($month) && !isset($district))
    {
        return $this->dengueMonthNumber($request);
    } elseif (isset($year) && isset($district) && !isset($month))
    {
        return $this->dengueDistrictYearNumber($request);
    } elseif (isset($year) && (!isset($month)) && (!isset($district)))
    {
        return $this->dengueYearNumber($request);
    } else
    {
        return ["result" => "false", "memo" => "Provided condition is incorrect"];
    }
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
        return ['result' => 'false', 'memo' => 'The queried data doesn\'t exists'];
    }
    $dengueDistrictMonthNumber = DB::table('dengue')
        ->whereYear('date', $request->year)
        ->whereMonth('date', $request->month)
        ->where('district', '=', $request->district)
        ->groupBy(DB::raw("year(date)", "month(date)", 'district'))
        ->count('district');

    return ['result' => 'true', 'monthlyDengueDistrictNumber' => $dengueDistrictMonthNumber];
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
        return ['result' => 'false', 'memo' => 'The queried data doesn\'t exists'];
    }
    $dengueMonthNumber = DB::table('dengue')
        ->whereYear('date', $request->year)
        ->whereMonth('date', $request->month)
        ->groupBy(DB::raw("year(date)", "month(date)"))
        ->count('district');

    $test = DB::table('dengue')
        ->select(DB::raw("count(district) number, month(date) month"))
        ->whereYear('date', $request->year)
        ->groupBy(DB::raw("year(date), month(date)"))
        ->orderBy("month", 'asc')
        ->get()->toArray();

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
        return ['result' => 'false', 'memo' => 'The queried data doesn\'t exists'];
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
        return ['result' => 'false', 'memo' => 'The queried data doesn\'t exists'];
    }
    $dengueYearNumber = DB::table('dengue')
        ->whereYear('date', $request->year)
        ->groupBy(DB::raw("year(date)"))
        ->groupBy(DB::raw("month(date)"))
        ->count('district')->first();

    return ['result' => 'true', 'yearlyDengueNumber' => $dengueYearNumber];
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
        return ['result' => 'false', 'memo' => 'The queried data doesn\'t exists'];
    }
    $dengueFatalityRate = DB::table('fatalityRate')
        ->where('date', '=', $request->year)
        ->first();

    return ['result' => 'true', 'dengueFatalityRate' => $dengueFatalityRate->fatalityRate];
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
        return ['result' => 'false', 'memo' => 'The queried data doesn\'t exists'];
    }
    $yearDistrictSumRainfall = DB::table('rainfall')
        ->whereYear('date', $request->year)
        ->where('district', '=', $request->district)
        ->groupBy(DB::raw("year(date)"))
        ->sum('rainfall');

    return ['result' => 'true', 'yearlyDistrictSumRainfall' => $yearDistrictSumRainfall];
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


    return ['result' => 'true', 'yearlySumRainfall' => $yearlySumRainfall];
}


