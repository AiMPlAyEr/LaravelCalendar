<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\CalendarController as Calendar;

class PageController extends Controller
{
    //
    public function index($year, $month) {
        $options = [
            "mode"              => 1, 
            "id"                => "calendar", 
            "tableClass"        => "table table-sm table-bordered", 
            "prevButtonClass"   => "btn btn-primary",
            "nxtButtonClass"    => "btn btn-warning",
            "titleClass"        => ""
        ];
        $data = ["data" => true];

        $calendar = (new Calendar($options, $data, $year, $month))->show();

        return view('page')->with(['calendar' => $calendar]);
    }
}
