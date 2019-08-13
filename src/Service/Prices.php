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

    public function standard()
    {
        return [
                'baby'=>$this->baby,
                'child'=>$this->child,
                'adult'=>$this->adult,
                'senior'=>$this->senior
        ];
    }

    public function half()
    {
        return [
            'baby'=>$this->baby * $this->reduc,
            'child'=>$this->child * $this->reduc,
            'adult'=>$this->adult * $this->reduc,
            'senior'=>$this->senior * $this->reduc
        ];
    }
}