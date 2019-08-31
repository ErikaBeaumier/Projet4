<?php
namespace App\Service;

use Ramsey\Uuid\Uuid;
use App\Entity\Choice;
use App\Service\Prices;
use App\Service\Ages;

class Summary
{

    //Id of the choice
    private $id;
    //DateTime of the visit
    private $visit;
    //True if tickets are half Days
    private $halfDay;
    //Number of tickets
    private $tickets;
    //uuid for trace and print bill
    private $uuid;
    //collection of tickets
    private $ticketcollection;
    //total prices
    private $totalPrices;

    

    public function __construct()
    {
       //do nothing at the moment
    }

    //Convert a choice into a summary by loading the choice into the summary object
    public function loadChoice($choice,$price,$ages)
    {
        //load summary data
        $this->id = $choice->getId();
        $this->visit = $choice->getVisit();
        $this->halfDay = $choice->getHalfDay();
        $this->tickets = $choice->getTickets();
        $this->uuid =  $choice->getUuid();
        //init the collection of tickets
        $this->ticketcollection =  [];
        //get visitors list from choice
        $visitors = $choice->getVisitors();
        //for each visitor in the list of visitors
        foreach ( $visitors as $visitor) {
            //create a new ticket
            $ticketWithPrice = new Ticket();
            //load data and calculate price. Add in totalPrices the price of tickets to obtain a full bill
            //the function $ticketWithPrice->loadTicket is returning the price of the ticket
            //the code '$this->totalPrices +=' make the sum of the current total and the new ticket
            $this->totalPrices += $ticketWithPrice->loadTicket($visitor,$price,$ages,$this->halfDay);
            //add tickets to the summary
            array_push($this->ticketcollection,$ticketWithPrice);
        }
   }



    //Accessor : get / set

    //Get if ot the choice
    public function getId(): ?int
    {
        return $this->id;
    }

    //Get date of the visit
    public function getVisit(): ?\DateTimeInterface
    {
        return $this->visit;
    }


    //Get true if the tickets are for half day or full day (false)
    public function getHalfDay(): ?bool
    {
        return $this->halfDay;
    }

    //Get number of tickets
    public function getTickets(): ?int
    {
        return $this->tickets;
    }

    //Get uuid for billing information
    public function getUuid()
    {
        return $this->uuid;
    }

    //Get the collection of tickets with prices by tickets
    public function getTicketcollection(): array
    {
        if($this->ticketcollection == null)
            return array();
        return $this->ticketcollection;
    }

    //Get the total prices of the choice
    public function gettotalPrices(): ?float
    {
        return $this->totalPrices;
    }

    
}