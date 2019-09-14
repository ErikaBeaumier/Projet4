<?php

namespace App\Tests\Service;

use App\Service\Prices;
use App\Service\Ages;

use PHPUnit\Framework\TestCase;

class PriceTest extends TestCase
{
    //Test  the price calculation
    public function testPriceCalculation()
    {
        $halfDay = true;
        $ticketType = 'adult';
        $ages = new Ages([4],[12],[60],[130]);
        $prices = new prices(0,8,16,12,0.5);
        $reduc = false;

        $result = $prices->getPrice($ticketType,$halfDay,$reduc);

        //Adult tickets, for hald day and reduction : result is : 16*0,5(halday) : 8
        $this->assertEquals(8,$result);

    }
}