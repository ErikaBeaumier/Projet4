<?php

namespace App\Tests\Service;

use App\Service\Prices;
use App\Service\Ages;
use App\Service\Ticket;
use App\Entity\Visitor;
use PHPUnit\Framework\TestCase;

class TicketTest extends TestCase
{
    //Test  the ticket price calcul 
    public function testPriceTicketsCalculationChildHalfDayReduc()
    {
        $halfDay = true;
        $ages = new Ages([4],[12],[60],[130]);
        $prices = new prices(0,8,16,12,0.5);
       
        $visitor = new Visitor();
        $visitor->setName("Test Last Name");
        $visitor->setFirstName("Test First Name");
        $visitor->setFirstName("Test First Name");
        $visitor->setBirthday(new \DateTime('02/31/2011')); 
        $visitor->setCountry("France");
        $visitor->setReduc(true);

 
        $ticket = new Ticket();
        $ticket->loadTicket($visitor,$prices,$ages,$halfDay);
        // Child half day with reduc :
        // 8 * 0,5 (hald day) * 0,5 (reduc) = 2
        $result =$ticket->getPrice();
        $this->assertEquals(2,$result);
    }

    public function testPriceTicketsCalculationAdultHalfDayReduc()
    {
        $halfDay = true;
        $ages = new Ages([4],[12],[60],[130]);
        $prices = new prices(0,8,16,12,0.5);
       
        $visitor = new Visitor();
        $visitor->setName("Test Last Name");
        $visitor->setFirstName("Test First Name");
        $visitor->setFirstName("Test First Name");
        $visitor->setBirthday(new \DateTime('02/31/2000')); 
        $visitor->setCountry("France");
        $visitor->setReduc(true);

 
        $ticket = new Ticket();
        $ticket->loadTicket($visitor,$prices,$ages,$halfDay);
        // Child half day with reduc :
        // 16 * 0,5 (hald day) * 0,5 (reduc) = 4
        $result =$ticket->getPrice();
        $this->assertEquals(4,$result);
    }
}