<?php

// tests/Util/CalculatorTest.php
namespace App\Tests\Service;

use App\Service\Ages;
use PHPUnit\Framework\TestCase;

class AgesTest extends TestCase
{
    //Test  the age verification
    public function testIsBaby()
    {
        $ages = new Ages([4],[12],[60],[130]);
       
        $result =$ages->isBaby(1);
        $this->assertTrue($result);
    }

    public function testIsChild()
    {
        $ages = new Ages([4],[12],[60],[130]);
       
        $result =$ages->isChild(7);
          
        $this->assertTrue($result);
    }

    public function testisAdult()
    {
        $ages = new Ages([4],[12],[60],[130]);
       
        $result =$ages->isAdult(20);
          
        $this->assertTrue($result);
    }

    public function testIsSenior()
    {
        $ages = new Ages([4],[12],[60],[130]);
       
        $result =$ages->isSenior(80);
          
        $this->assertTrue($result);
    }

    public function testGetAges()
    {
        $goodResult = array([4],[12],[60],[130]);
        $ages = new Ages([4],[12],[60],[130]);
        $listAges = $ages->getAges();
        $result = false;
        if($listAges[0][0] == $goodResult[0][0] 
            && $listAges[1][0] == $goodResult[1][0] 
            && $listAges[2][0] == $goodResult[2][0] 
            && $listAges[3][0] == $goodResult[3][0])
                $result = true;
        
        $this->assertTrue($result);
    }

    
}

