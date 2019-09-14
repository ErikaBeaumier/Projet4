<?php


namespace App\Tests\Service;

use App\Service\Prices;
use App\Service\Ages;
use App\Service\Ticket;
use App\Service\Summary;
use App\Entity\Visitor;
use App\Entity\Choice;
use PHPUnit\Framework\TestCase;

class SummaryTest extends TestCase
{
    //Test  the summary calcul 
    public function testCalculatePrice()
    {
        $halfDay = true;
        $ages = new Ages([4],[12],[60],[130]);
        $prices = new prices(0,8,16,12,0.5);

        $choice = new Choice();
        $choice->setVisit(new \DateTime('10/28/2019'));
        $choice->setHalfDay($halfDay);
        $choice->setTickets(2);

        $visitor = new Visitor();
        $visitor->setName("Test Last Name");
        $visitor->setFirstName("Test First Name");
        $visitor->setBirthday(new \DateTime('02/31/2011')); 
        $visitor->setCountry("France");
        $visitor->setReduc(true);
       
        $choice->addVisitor($visitor);

        $visitor = new Visitor();
        $visitor->setName("Test Last Name 2");
        $visitor->setFirstName("Test First Name 2");
        $visitor->setBirthday(new \DateTime('02/31/2000')); 
        $visitor->setCountry("France");
        $visitor->setReduc(false);

        $choice->addVisitor($visitor);


        $summary = new Summary();
        $summary->loadChoice($choice,$prices,$ages);
        //Child vith reduc on half day : 2 Euros
        //Adult without reduc on hald day : 8 Euros
        //Supposed total : 10 euros
        $result = $summary->gettotalPrices();
        $this->assertEquals(10,$result);
    }
}