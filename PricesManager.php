<?php
/*
namespace App\Service;

class PricesManager{

	private $tarifs;

	public function __construct($tarifs, $ages, $coef){

		$this->tarifs=$tarifs;
		$this->ages=$ages;
		$this->coef=$coef;
	}

	public function getPrice($age, $reduced){ $coef = 1; if ($reduced) $coef = $this->coef; if ($age < $this->ages->baby) return $this->tarifs->baby * $coef; if ($age < $this->ages->child) return $this->tarifs->child * $coef; if ($age > $this->ages->senior) return $this->tarifs->senior* $coef; return $this->tarifs->normal*$coef; }
}
*/