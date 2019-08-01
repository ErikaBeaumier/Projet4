<?php
namespace App\Service;

class Disabled
{

    public function __construct($daysOfWeek, $staticDatesText, $staticDates)
    {
        $this->daysOfWeek = $daysOfWeek;
        $this->staticDatesText = $staticDatesText;
        $this->staticDates = $staticDates;
    }

    public function dateDisabled()
    {
        $messages = 'Cher Visiteur, le musée est fermé le';

        return $messages;
    }
}