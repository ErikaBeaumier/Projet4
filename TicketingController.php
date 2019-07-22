<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Second;

class TicketingController extends AbstractController
{
    /**
     * @Route("/ticketing", name="ticketing")
     */
    public function index()
    {
        return $this->render('ticketing/index.html.twig', [
            'controller_name' => 'TicketingController',
        ]);
    }

    /**
    * @Route("/", name="home")
    */
    public function home()
    {
    	return $this->render('ticketing/home.html.twig',
    		[
    			'Title' => "Bienvenue sur la billetterie du Musée du Louvre",
    		]);
    }

    /**
    * @Route("/prices", name="prices")
    */
    public function prices()
    {
    	return $this->render('ticketing/prices.html.twig');
    }

    /**
    * @Route("/ticketing/second", name="second")
    */
    public function second()
    {
        return $this->render('ticketing/second.html.twig');
    }

    /**
    * @Route("/ticketing/summary", name="summary")
    */
    public function summary()
    {
        $repo = $this->getDoctrine()->getRepository(Second::class);

        $seconds = $repo->findAll();

        return $this->render('ticketing/summary.html.twig', ['seconds'=>$seconds]);
    }
}