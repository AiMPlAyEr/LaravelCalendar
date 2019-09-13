<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CalendarController extends Controller
{
    /*
        Calendar Settings:
        mode: 1 (tradional calendar), 2 (shows months on left side)
        id: set an ID for your calendar
        class: set one or multiple classes
    */

    public function __construct($options, $data, $year, $month) {
        $this->error = "";
        $areOptionsValid    = !empty($options) && is_array($options) ? true : false;
        $isDataValid        = !empty($data) && is_array($data) ? true : false;
        $isYearValid        = !empty($year) && is_numeric($year) ? true : false;
        $isMonthValid       = !empty($month) && is_numeric($month) ? true : false;
        
        if ($areOptionsValid) {
            $this->options  = (object) $options;
        } else {
            $this->error .= $this->buildErrorMessage("variable options", Null);
        }

        if ($isDataValid) {
            $this->data  = $data;
        } else {
            $this->error .= $this->buildErrorMessage("variable data", Null);
        }

        if ($isYearValid) {
            $this->year  = (int) $year;
        } else {
            $this->error .= $this->buildErrorMessage("variable year", Null);
        }

        if ($isMonthValid) {
            $month = $month < 10 ? sprintf('%02d', $month) : $month;
            $this->month  = $month;
        } else {
            $this->error .= $this->buildErrorMessage("variable month", Null);
        }
    }

    public function show() {
        if(!empty($this->error)) {
            return $this->error;
        }

        if ($this->options->mode == 1) {
            return $this->buildRegularCalendar();
        } elseif ($this->options->mode == 2) {
            return $this->buildHorizontalCalendar();
        }
    }

    private function buildRegularCalendar() {
        $navigation = $this->getRegularNavigation();
        $content    = $this->getRegularDays();
        $footer     = $this->getRegularFooter();

        return $navigation.$content.$footer;
    }

    private function getRegularNavigation() {
        $date;
        if ($this->year == NULL ||$this->month == NULL) {
            $date = date('Y-m-d');
        } else {
            $date = date($this->year.'-'.$this->month.'-d');
        }

        $writtenMonth = date('F', strtotime($date));

        $prevYear   = $this->month == 1 ? $this->year - 1 : $this->year;
        $prevMonth  = $this->month == 1 ? 12 : $this->month-1; 

        $nxtYear    = $this->month == 12 ? $this->year + 1 : $this->year;
        $nxtMonth   = $this->month == 12 ? 01 : $this->month + 1;

        $elementOpeningTag = '<div class="navigation">'; 
        $elementClosingTag = '</div>'; 

        $prevButtonClass    = isset($this->options->prevButtonClass) ? $this->options->prevButtonClass : "";
        $nxtButtonClass     = isset($this->options->nxtButtonClass) ? $this->options->nxtButtonClass : "";
        $titleClass         = isset($this->options->titleClass) ? $this->options->titleClass : "";

        $prevButton = '<a href="'.asset('/page/'.$prevYear.'/'.$prevMonth).'"><button class="prev '.$prevButtonClass.'">Previous</button></a>';
        $label      = '<div class="calendar-title '.$titleClass.'">'.$this->year.' '.$writtenMonth.'</div>';
        $nxtButton  = '<a href="'.asset('/page/'.$nxtYear.'/'.$nxtMonth).'"><button class="nxt '.$nxtButtonClass.'">Next</button></a>'; 

        $element = $elementOpeningTag.$prevButton.$label.$nxtButton.$elementClosingTag;

        return $element;
    }

    private function getRegularDays() {
        $getWeekDays    = ["Montag", "Dienstag", "Mittwoch", "Donnerstag", "Freitag", "Samstag", "Sonntag"];
        $getDaysOfMonth = ["31", "28", "31", "30", "31", "30", "31", "31", "30", "31", "30", "31"];

        $tableClass = isset($this->options->tableClass) ? $this->options->tableClass : "";

        $gridOpeningTag = '<table class="table '.$tableClass.'">';
        $thead = '<thead><tr>';
        for($weekday = 0; $weekday <= 6; $weekday++) {
            $thead .= '<th>'.$getWeekDays[$weekday].'</th>';
        }
        $thead .= '</tr></thead>'; 

        $tbody = '<tbody>';

        $counter = 1;
        $maxCounter = $getDaysOfMonth[(int)date('m')-1];
        for($row = 1; $row <= 6; $row++) {
            $tbody .= '<tr>';
            for($column = 1; $column <= 7; $column++) {
                if($counter == 1 && $row == 1) {
                    if($column == date('N', strtotime($this->year.'-'.$this->month.'-01'))) {
                        $tbody .= '<td>'.$counter.'</td>';
                        $counter++;
                    } else {
                        $tbody .= '<td class="greyedOut">-</td>';
                    }
                } else {
                    if($counter <= $maxCounter) {
                        $tbody .= '<td>'.$counter.'</td>';
                    } else {
                        $tbody .= '<td class="greyedOut">-</td>';
                    }
                    $counter++;
                }
            }
            $tbody .= '</tr>';
        }
        $tbody .= '</tbody>';


        $gridClosingTag = '</table>';

        $grid = $gridOpeningTag.$thead.$tbody.$gridClosingTag;

        return $grid;
    }

    private function getRegularFooter() {

    }

    private function buildHorizontalCalendar() {

    }

    private function checkForEvents() {

    }

    private function buildErrorMessage($field, $message) {
        if(!isset($message)) {
            $message = "Error: Couldn't create calendar because there seems to be something wrong with <pre style='display: inline;'>{field}</pre>.";
        }
        return '<div class="danger">'.str_replace("{field}", $field, $message).'</div>';
    }
}
