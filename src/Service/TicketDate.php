<?php
namespace App\Service;

use App\Service\Schedule;

class TicketDate
{

    //A service to help the test of the selected date by the user

    private $closedDays;
    private $soldoutDays;
    private $notWorkingDays;
    private $schedule;

    //Load the helper, If notWorkding Days is null, load default notworkingdays
    public function __construct($closedDays,$soldoutDays,$notWorkingDays,$scheduleParam)
    {
        $this->closedDays = $closedDays;
        $this->soldoutDays = $soldoutDays;
        $this->schedule = $scheduleParam;
        if($notWorkingDays == null)
             $this->notWorkingDays = $this->loadNotWorkingDays(new \DateTime());
        else
            $this->notWorkingDays = $notWorkingDays;
    }

    //Default notWorkingDays
    private function loadNotWorkingDays($currentDate)
    {
        $currentYear = $currentDate->format("Y");   
        $currentDay = $currentDate->format("j");  
        $currentMonth = $currentDate->format("m");  

        
        $easterDate  = easter_date($currentYear);
        $easterDay   = date('j', $easterDate);
        $easterMonth = date('n', $easterDate);
        $easterYear   = date('Y', $easterDate);

        $holidays = array(
            // Dates fixes
            \DateTime::createFromFormat('m-j-Y', '1-1-'.$currentYear),// 1er janvier
            \DateTime::createFromFormat('m-j-Y', '5-1-'.$currentYear), // Fête du travail
            \DateTime::createFromFormat('m-j-Y', '5-8-'.$currentYear),// Victoire des alliés
            \DateTime::createFromFormat('m-j-Y', '7-14-'.$currentYear),// Fête nationale
            \DateTime::createFromFormat('m-j-Y', '8-15-'.$currentYear),// Assomption
            \DateTime::createFromFormat('m-j-Y', '11-1-'.$currentYear),// Toussaint
            \DateTime::createFromFormat('m-j-Y', '11-11-'.$currentYear),// Armistice
            \DateTime::createFromFormat('m-j-Y', '12-25-'.$currentYear),// Noel
            // Dates variables
            \DateTime::createFromFormat('m-j-Y', $easterMonth.'-'.($easterDay + 1+1).'-'.$currentYear),
            \DateTime::createFromFormat('m-j-Y', $easterMonth.'-'.($easterDay + 39+1).'-'.$currentYear),
            \DateTime::createFromFormat('m-j-Y', $easterMonth.'-'.($easterDay + 50+1).'-'.$currentYear),
            );
        
        $this->notWorkingDays = $holidays ;
    }

    //Test if the day is open or not
    public function isOpen($selectedDate)
    {

        $this->loadNotWorkingDays($selectedDate);



        //Test closed days
        $day = $selectedDate->format("w");
        if(in_array($day, $this->closedDays))
        {
            
            return false;
        }

        //Test sold ou days
        foreach ($this->soldoutDays as $value){
            if(\DateTime::createFromFormat('Y-m-j', $value)->format("j") == $selectedDate->format("j") && \DateTime::createFromFormat('Y-m-j', $value)->format("m") == $selectedDate->format("m"))
               { 
                
                   return false;
               }

        }
        
        //Test not working days
        foreach ($this->notWorkingDays as $value){
            if($value->format("j") == $selectedDate->format("j") && $value->format("m") == $selectedDate->format("m"))
            { 
                
                return false;
            }
        }

        
        // Test anté date
        $today = new \DateTime('today');
        //clean date 
        $today =  \DateTime::createFromFormat('Y-m-d', date_format($today, 'Y-m-d'));
        //cleanse date 2
        $shortSelectedDate = \DateTime::createFromFormat('Y-m-d', date_format($selectedDate, 'Y-m-d'));

        // On compare les deux dates
        if($today > $shortSelectedDate)
        { 
            
            return false;
        }

        if($today == $shortSelectedDate)
        {
            //test if before the limite hour
            $finalHour = $this->schedule->getClosedHourTickets();
            if(date('H') >= $finalHour)
            { 
                
                return false;
            }
        }

        $maxYear = $today->format("Y");   
        $maxDay = $today->format("j");  
        $maxMonth = $today->format("m");  

        //if date is to far in the futur : default 1 year
        $limiteDate = \DateTime::createFromFormat('m-j-Y', $maxMonth.'-'.$maxDay.'-'.($maxYear+1));

        if( $limiteDate < $shortSelectedDate)
        { 
            
            return false;
        }

        //Could use this date
        return true;
    }


}