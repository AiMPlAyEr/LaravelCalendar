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
            $this->data  = (object) $data;
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

        $prevButton = '<a href="'.asset('/page/'.$prevYear.'/'.$prevMonth).'"><button class="prev '.$this->doesOptionExist($this->options->prevButtonClass).'">Previous</button></a>';
        $label      = '<div class="calendar-title '.$this->doesOptionExist($this->options->titleClass).'">'.$this->year.' '.$writtenMonth.'</div>';
        $nxtButton  = '<a href="'.asset('/page/'.$nxtYear.'/'.$nxtMonth).'"><button class="nxt '.$this->doesOptionExist($this->options->nxtButtonClass).'">Next</button></a>'; 

        $element = $elementOpeningTag.$prevButton.$label.$nxtButton.$elementClosingTag;

        return $element;
    }

    private function getRegularDays() {
        $getWeekDays    = ["Montag", "Dienstag", "Mittwoch", "Donnerstag", "Freitag", "Samstag", "Sonntag"];
        $getDaysOfMonth = ["31", "28", "31", "30", "31", "30", "31", "31", "30", "31", "30", "31"];

        if($this->year % 4 == 0) {
            $getDaysOfMonth[1] = 29;
        }

        $gridOpeningTag = '<table class="table '.$this->doesOptionExist($this->options->tableClass).'">';
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
                $todaysEvents = [];
                foreach ($this->data as $key => $value) {
                    if(date('Y-m-d', strtotime($value["begin"])) < date('Y-m-d', strtotime($this->year."-".$this->month."-".$counter)) && date('Y-m-d', strtotime($value["end"])) > date('Y-m-d', strtotime($this->year."-".$this->month."-".$counter))) {
                        array_push($todaysEvents, ['informations' => $value, 'type' => 'center']);
                    } elseif(date('Y-m-d', strtotime($value["begin"])) === date('Y-m-d', strtotime($this->year."-".$this->month."-".$counter)) && date('Y-m-d', strtotime($value["end"])) === date('Y-m-d', strtotime($this->year."-".$this->month."-".$counter))) {
                        array_push($todaysEvents, ['informations' => $value, 'type' => 'single']);
                    } elseif(date('Y-m-d', strtotime($value["begin"])) == date('Y-m-d', strtotime($this->year."-".$this->month."-".$counter))) {
                        array_push($todaysEvents, ['informations' => $value, 'type' => 'start']);
                    } elseif(date('Y-m-d', strtotime($value["end"])) == date('Y-m-d', strtotime($this->year."-".$this->month."-".$counter))) {
                        array_push($todaysEvents, ['informations' => $value, 'type' => 'end']);
                    }
                }

                if($counter == 1 && $row == 1) {
                    if($column == date('N', strtotime($this->year.'-'.$this->month.'-01'))) {
                        $tbody      .= '<td>'.$counter;
                        $eventClass = Null;
                        $matches    = 0;
                        foreach ($todaysEvents as $value) {
                            foreach($this->options->eventClass as $classKey => $classValue) {
                                $newEvent = $value["informations"]["title"].'<div class="hiddenDescription">'.$value["informations"]["description"].'</div>';
                                if(strpos($newEvent, $classValue["key"]) !== false) {
                                    $tbody .= '<span class="event '.$classValue["className"].' '.$value["type"].'">'.$newEvent.'</span>';
                                    $matches++;
                                }
                            }

                            if($matches <= 0) {
                                $tbody .= '<span class="event '.$value["type"].'">'.$newEvent.'</span>';
                            }
                        }
                        $tbody .= '</td>';
                        $counter++;
                    } else {
                        $tbody .= '<td class="greyedOut">-</td>';
                    }
                } else {
                    if($counter <= $maxCounter) {
                        $tbody .= '<td>'.$counter;
                        $eventClass = Null;
                        $matches = 0;
                        foreach ($todaysEvents as $key => $value) {
                            foreach($this->options->eventClass as $classKey => $classValue) {
                                $newEvent = $value["informations"]["title"].'<div class="hiddenDescription">'.$value["informations"]["description"].'</div>';
                                if(strpos($newEvent, $classValue["key"]) !== false) {
                                    $tbody .= '<span class="event '.$classValue["className"].' '.$value["type"].'">'.$newEvent.'</span>';
                                    $matches++;
                                }
                            }

                            if($matches <= 0) {
                                $tbody .= '<span class="event '.$value["type"].'">'.$newEvent.'</span>';
                            }
                        }
                        $tbody .= '</td>';
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
        $footer = '<div class="container"><ul class="row">';

        foreach($this->options->eventClass as $classKey => $classValue) {
            $footer .= '<li class="list-group-item col-6">'.$this->doesOptionExist($classValue["name"]).'<span class="event single '.$this->doesOptionExist($classValue["className"]).' legendPreview"></span></li>';
        }

        $footer .= '</url></div>';

        return $footer;
    }

    private function buildHorizontalCalendar() {
        $navigation = $this->getHorizontalNavigation();
        $content    = $this->getHorizontalContent();
        $footer     = $this->getHorizontalFooter();

        return $navigation.$content.$footer;
    }

    private function getHorizontalNavigation() {
        $date;
        $rangeDate;
        if ($this->year == NULL ||$this->month == NULL) {
            $date = date('Y-m-d');
            $rangeDate = date('Y-m-d', strtotime("-1 months"));
        } else {
            $date = date($this->year.'-'.$this->month.'-d');
            $rangeDate = date($this->year.'-'.($this->month + 1).'-d');
        }

        $writtenMonth = date('F', strtotime($date));

        $prevYear   = $this->month == 1 ? $this->year - 1 : $this->year;
        $prevMonth  = $this->month == 1 ? 12 : $this->month-1; 

        $nxtYear    = $this->month == 12 ? $this->year + 1 : $this->year;
        $nxtMonth   = $this->month == 12 ? 01 : $this->month + 1;

        $rangeYear  = $this->month - 12 < 0 ? $this->year - 1 : $this->year;
        $rangeMonth = date('F', strtotime($rangeDate));

        $elementOpeningTag = '<div class="navigation">'; 
        $elementClosingTag = '</div>'; 

        $prevButton = '<a href="'.asset('/page/'.$prevYear.'/'.$prevMonth).'"><button class="prev '.$this->doesOptionExist($this->options->prevButtonClass).'">Previous</button></a>';
        $label      = '<div class="calendar-title '.$this->doesOptionExist($this->options->titleClass).'">'.$rangeYear.' '.$rangeMonth.' - '.$this->year.' '.$writtenMonth.'</div>';
        $nxtButton  = '<a href="'.asset('/page/'.$nxtYear.'/'.$nxtMonth).'"><button class="nxt '.$this->doesOptionExist($this->options->nxtButtonClass).'">Next</button></a>'; 

        $element = $elementOpeningTag.$prevButton.$label.$nxtButton.$elementClosingTag;

        return $element;
    }

    private function getHorizontalContent() {
        $gridOpeningTag = '<div class="'.$this->doesOptionExist($this->options->tableWrapperClass).'"><table class="'.$this->doesOptionExist($this->options->tableClass).'"><tbody>';
        $gridBody       = "";
        $currMonth      = $this->month+1;
        $currYear       = $this->year;
        $getDaysOfMonth = ["31", "28", "31", "30", "31", "30", "31", "31", "30", "31", "30", "31"];

        for ($rows=1; $rows <= 12; $rows++) { 
            $currYear   = $currMonth == 1 ? $currYear - 1 : $currYear;
            $currMonth  = $currMonth == 1 ? 12 : $currMonth - 1;
            $currRow    = "";

            $currRow .= '<tr><th>'.date('F', strtotime($currYear.'-'.$currMonth.'-01')).'</th>'; 

            for ($i=1; $i <= 31; $i++) { 
                if($i <= $getDaysOfMonth[$currMonth-1]) {
                    if(($currMonth - 1) == 1 && $currYear % 4 == 0) {
                        $getDaysOfMonth[1] = 29;
                    }
                    $currRow .= '<td>'.$i;
                    if (!empty($this->data)) {
                        $todaysEvents = [];
                        foreach ($this->data as $key => $value) {
                            
                            if(date('Y-m-d', strtotime($value["begin"])) < date('Y-m-d', strtotime($currYear."-".$currMonth."-".$i)) && date('Y-m-d', strtotime($value["end"])) > date('Y-m-d', strtotime($currYear."-".$currMonth."-".$i))) {
                                array_push($todaysEvents, ['informations' => $value, 'type' => 'center']);
                            } elseif(date('Y-m-d', strtotime($value["begin"])) === date('Y-m-d', strtotime($currYear."-".$currMonth."-".$i)) && date('Y-m-d', strtotime($value["end"])) === date('Y-m-d', strtotime($currYear."-".$currMonth."-".$i))) {
                                array_push($todaysEvents, ['informations' => $value, 'type' => 'single']);
                            } elseif(date('Y-m-d', strtotime($value["begin"])) == date('Y-m-d', strtotime($currYear."-".$currMonth."-".$i))) {
                                array_push($todaysEvents, ['informations' => $value, 'type' => 'start']);
                            } elseif(date('Y-m-d', strtotime($value["end"])) == date('Y-m-d', strtotime($currYear."-".$currMonth."-".$i))) {
                                array_push($todaysEvents, ['informations' => $value, 'type' => 'end']);
                            }
                        }

                        usort($todaysEvents, function($a, $b) {
                            if(strtotime($a["informations"]["end"]) > strtotime($b["informations"]["end"])) {
                                return -1;
                            } elseif(strtotime($a["informations"]["end"]) < strtotime($b["informations"]["end"])) {
                                return 1;
                            } else {
                                return strcmp($a["informations"]["title"], $b["informations"]["title"]);
                            }
                        });

                        foreach ($todaysEvents as $value) {
                            $currRow .= '<span class="event '.$value["type"].' ';
                            foreach($this->options->eventClass as $classKey => $classValue) {
                                $newEvent = '<div class="hiddenDescription">'.$value["informations"]["title"].$value["informations"]["description"].'</div>';
                                if(strpos($newEvent, $classValue["key"]) !== false) {
                                    $currRow .= $classValue["className"];
                                }
                            }
                            $currRow .= '"></span>';
                        }
                    }
                    $currRow .= '</td>';
                } else {
                    $currRow .= '<td></td>';
                }
            }

            $currRow .= '</tr>';
            $gridBody = $currRow.$gridBody;
        }

        $gridClosingTag = '</tbody></table></div>';

        return $gridOpeningTag.$gridBody.$gridClosingTag;

    }

    private function getHorizontalFooter() {
        $footer = '<div class="container"><ul class="row">';

        foreach($this->options->eventClass as $classKey => $classValue) {
            $footer .= '<li class="list-group-item col-6">'.$this->doesOptionExist($classValue["name"]).'<span class="event single '.$this->doesOptionExist($classValue["className"]).' legendPreview"></span></li>';
        }

        $footer .= '</url></div>';

        return $footer;
    }

    private function buildErrorMessage($field, $message) {
        if(!isset($message)) {
            $message = "Error: Couldn't create calendar because there seems to be something wrong with <pre style='display: inline;'>{field}</pre>.";
        }
        return '<div class="danger">'.str_replace("{field}", $field, $message).'</div>';
    }

    private function doesOptionExist(&$value) {
        if($value !== NULL && !empty($value)) {
            return $value;
        } else {
            return "";
        }
    }
}
