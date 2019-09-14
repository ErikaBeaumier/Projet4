<?php
namespace App\Service;

class Ages
{
    public $babyAge;
    public $childAge;
    public $adultAge;
    public $seniorAge;


    public function __construct($babyAge, $childAge, $adultAge, $seniorAge)
    {
        $this->babyAge = $babyAge;
        $this->childAge = $childAge;
        $this->adultAge = $adultAge;
        $this->seniorAge = $seniorAge;

    }

    public function getAges()
    {
        return array($this->babyAge,$this->childAge,$this->adultAge,$this->seniorAge);
    }

    public function isBaby($age)
    {
      
        if($age < $this->babyAge[0])
        {
            return true;
        }
        return false;
    }

    public function isChild($age)
    {
        if($age >= $this->babyAge[0] && $age < $this->childAge[0])
            return true;
        return false;
    }

    public function isAdult($age)
    {
        if($age >= $this->childAge[0] && $age < $this->adultAge[0])
            return true;
        return false;
    }

    public function isSenior($age)
    {
        if($age >= $this->adultAge[0])
            return true;
        return false;
    }
}