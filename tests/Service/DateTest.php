<?php

// tests/Util/CalculatorTest.php
namespace App\Tests\Service;

use App\Service\Schedule;
use App\Service\TicketDate;
use PHPUnit\Framework\TestCase;

class DateTest extends TestCase
{
    //Test  the date selected code
    public function testIsOpen()
    {
        $schedule = new schedule(null, null, [0,2],17,14);
        $datesSoldOut = ["2020-01-02"];
        $helperTicketDate = new TicketDate($schedule->getClosedDay(),$datesSoldOut,null, $schedule);
        $canBuyTickets = $helperTicketDate->isOpen(\DateTime::createFromFormat('m-j-Y', "01-03-2020"));
        $this->assertTrue($canBuyTickets);
    }
}