<?php
namespace App\Service;

class Schedule
{
    public $daysOfWeek;
    public $staticDatesText;
    public $closedDay;
    public $closeHourTickets;
    public $halfTicketsMaxHour;

    public function __construct($daysOfWeek, $staticDatesText, $closedDay,$closeHourTickets,$halfTicketsMaxHour )
    {
        $this->daysOfWeek = $daysOfWeek;
        $this->staticDatesText = $staticDatesText;
        $this->closedDay = $closedDay;
        $this->closeHourTickets = $closeHourTickets;
        $this->halfTicketsMaxHour = $halfTicketsMaxHour;
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

    public function getClosedHourTickets()
    {
        return $this->closeHourTickets;
    }

    public function getHalfTicketsMaxHour()
    {
        return $this->halfTicketsMaxHour;
    }
}