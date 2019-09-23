<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\CalendarController as Calendar;

class PageController extends Controller
{
    //
    public function index($year, $month) {
        $options = [
            "mode"              => 2, 
            "id"                => "calendar", 
            "tableWrapperClass" => "table-responsive",
            "tableClass"        => "table table-sm table-striped table-custom", 
            "prevButtonClass"   => "btn btn-flat btn-success",
            "nxtButtonClass"    => "btn btn-flat btn-success",
            "titleClass"        => "",
            "eventClass"        => [
                ["name" => "Reguläre Events", "key" => "1", "className" => "type-1"],
                ["name" => "Besondere Events... der Name ist aber relativ lang", "key" => "2", "className" => "type-2"],
                ["name" => "Ein weiterer Test", "key" => "T", "className" => "type-3"],
                ["name" => "Grün!", "key" => "Fahrt ins Grüne", "className" => "type-4"]
                ]
        ];
        $data = [
            [
                "title" => "test 1", 
                "description" => "ein geniales Event!", 
                "begin" => "17.09.2019",
                "end" => "28.09.2019"
            ],
            [
                "title" => "test 2", 
                "description" => "ein geniales Event!", 
                "begin" => "17.09.2019",
                "end" => "19.10.2019"
            ],
            [
                "title" => "test 3", 
                "description" => "ein weiteres geniales Event!", 
                "begin" => "24.09.2019",
                "end" => "24.09.2019"
            ],
            [
                "title" => "Test 3", 
                "description" => "ein weiteres geniales Event!", 
                "begin" => "17.09.2019",
                "end" => "19.10.2019"
            ],
            [
                "title" => "Fahrt ins Grüne", 
                "description" => "ein weiteres geniales Event!", 
                "begin" => "26.09.2019",
                "end" => "26.09.2019"
            ],
            [
                "title" => "test 2", 
                "description" => "ein geniales Event!", 
                "begin" => "17.01.2020",
                "end" => "19.10.2021"
            ],
            [
                "title" => "test 43", 
                "description" => "ein weiteres geniales Event!", 
                "begin" => "01.04.2020",
                "end" => "01.09.2020"
            ],
            [
                "title" => "Test 44", 
                "description" => "ein weiteres geniales Event!", 
                "begin" => "12.05.2020",
                "end" => "16.06.2020"
            ],
            [
                "title" => "Fahrt ins Grüne", 
                "description" => "ein weiteres geniales Event!", 
                "begin" => "05.01.2020",
                "end" => "07.01.2020"
            ]
        ];

        $calendar = (new Calendar($options, $data, $year, $month))->show();

        return view('page')->with(['calendar' => $calendar]);
    }
}
