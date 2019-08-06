<?php
namespace App\Service;

class Prices
{
    public $baby;
    public $child;
    public $adult;
    public $senior;
    public $reduc;

    public function __construct($baby, $child, $adult, $senior, $reduc)
    {
        $this->baby = $baby;
        $this->child = $child;
        $this->adult = $adult;
        $this->senior = $senior;
        $this->reduc = $reduc;
    }

    /*public function normal()
    {
        return $this->$baby;
                    ->$child;
                    ->$adult;
                    ->$senior;
    }*/
}