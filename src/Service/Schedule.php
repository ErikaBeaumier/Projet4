<?php
namespace App\Service;

class Schedule
{
    public $daysOfWeek;
    public $staticDatesText;

    public function __construct($daysOfWeek, $staticDatesText)
    {
        $this->daysOfWeek = $daysOfWeek;
        $this->staticDatesText = $staticDatesText;
    }

    public function opening()
    {
        return Implode(" ", $this->daysOfWeek);
    }

    public function closing()
    {
        return Implode(" ", $this->staticDatesText);
    }
}