<?php
namespace App\Service;

class Ages
{
    public $babyAge;
    public $childAge;
    public $adultAge;
    public $seniorAge;

    public function __construct($babyAge, $childAge, $adultAge, $seniorAge, $reducAge)
    {
        $this->babyAge = $babyAge;
        $this->childAge = $childAge;
        $this->adultAge = $adultAge;
        $this->seniorAge = $seniorAge;
        $this->reducAge = $reducAge;
    }
}