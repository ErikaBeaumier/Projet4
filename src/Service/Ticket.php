<?php
namespace App\Service;
use App\Service\Prices;
use App\Service\Ages;

class Ticket
{

    private $id;
    private $name;
    private $firstName;
    private $birthday;
    private $country;
    private $reduc;
    private $price;
    private $tarifType;

    public function __construct()
    {
        //do nothing at the moment
    }

    //convert a visitor into a tickers with prices calcultated by age, reduction and day/halfday
    public function loadTicket($visitor,$prices,$ages,$isHalfDay): float
    {
        //load visitor into tickets
        $this->id = $visitor->getId();
        $this->name = $visitor->getName();
        $this->firstName = $visitor->getFirstName();
        $this->birthday = $visitor->getBirthday();
        $this->country = $visitor->getCountry();
        $this->reduc = $visitor->getReduc();

        
        //calculate the price
        $this->loadPrice($prices,$ages,$isHalfDay);
    
        //return prices of the tickets
        return $this->price;
    }

    //calcultate the price by calcultated by age, reduction and day/halfday
    private function loadPrice($prices,$ages,$isHalfDay)
    {
        $age = $this->age($this->birthday);
     
        //by default
        $this->price = $prices->getPrice('adult',$isHalfDay, $this->reduc);  
        $this->tarifType = "adult";
        //set reduc
        if($ages->isBaby($age) == true)
        {
            $this->tarifType = "baby";
            $this->price = $prices->getPrice('baby',$isHalfDay, $this->reduc);
        }
        if($ages->isChild($age) == true)
        {
            $this->tarifType = "child";
            $this->price = $prices->getPrice('child',$isHalfDay, $this->reduc);    
        }     
        if($ages->isSenior($age) == true)
        {
            $this->tarifType = "senior";
            $this->price = $prices->getPrice('senior',$isHalfDay, $this->reduc);        
        }
    }

private function age($date) {
   $birthYear = intval($date->format('Y'));
   $birthMonth = intval($date->format('m'));
   $now = new \DateTime();;
   $nowYear = intval($now->format('Y'));
   $nowmonth = intval($now->format('m'));
   
   $ageCalculated = $nowYear - $birthYear;

   if($nowmonth < $birthMonth)
    return $ageCalculated-1;
   else
    return $ageCalculated;
}   

    //accessor

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTarifType() :?string
    {
        return $this->tarifType;
    }


    public function getName(): ?string
    {
        return $this->name;
    }



    public function getFirstName(): ?string
    {
        return $this->firstName;
    }


    public function getBirthday(): ?\DateTimeInterface
    {
        return $this->birthday;
    }



    public function getCountry(): ?string
    {
        return $this->country;
    }



    public function getReduc(): ?string
    {
        return $this->reduc;
    }



    public function getChoiceuuid()
    {
        return $this->choiceuuid;
    }

   
    public function getPrice(): ?float
    {
        return $this->price;
    }


}