<?php
namespace App\Service;

class Schedule
{
    public $daysOfWeek;
    public $staticDatesText;
    public $closedDay;

    public function __construct($daysOfWeek, $staticDatesText, $closedDay)
    {
        $this->daysOfWeek = $daysOfWeek;
        $this->staticDatesText = $staticDatesText;
        $this->closedDay = $closedDay;
    }

    public function opening()
    {
        return $this->daysOfWeek;
    }

    public function closing()
    {
        return $this->staticDatesText;
    }

    public function getClosedDay()
    {
        return $this->closedDay;
    }
}