<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\Second;

class SecondFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        for($i = 1; $i <= 10; $i++)
        {
        	$second = new Second();
        	$second->setName("Nom n°$i")
        		   ->setFirstName("Prénom n°$i")
        		   ->setBirthDate(new \DateTime())
        		   ->setCountry("Pays n°$i");

        	$manager->persist($second);
        }

        $manager->flush();
    }
}