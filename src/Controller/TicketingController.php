<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Choice;
use App\Repository\ChoiceRepository;
use App\Form\ChoiceType;
use App\Entity\Visitor;
use App\Repository\VisitorRepository;
use App\Form\VisitorType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\Common\Persistence\ObjectManager;
use App\Service\Schedule;
use App\Service\Prices;
use App\Service\Ages;
use Ramsey\Uuid\Uuid;
//use Symfony\Component\Validator\Constraints\Uuid;

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
     * @Route("/ticketing", name="ticketing")
     */    
    public function ChoiceForm(Choice $choice = null, Request $request, ObjectManager $manager)
    {
        if(!$choice)
        {
            $choice = new Choice();
        }            

        $form = $this->createForm(ChoiceType::class, $choice);

        $form->handleRequest($request);

        $choice->uuid=Uuid::uuid4();

        if($form->isSubmitted() && $form->isValid())
        {
            $manager->persist($choice);
            $manager->flush();

            return $this->redirectToRoute('visitor', ['id' => $choice->getId()]);
        }

        return $this->render('ticketing/index.html.twig', ['formChoice' => $form->createView(), 'editMode' => $choice->getId() !== null
        ]);
    }


    /**
    * @Route("/", name="home")
    */
    public function home(Schedule $schedule)
    {
        return $this->render('ticketing/home.html.twig',
                            [
                                'Opening' => $schedule->opening(),
                                'Closing' => $schedule->closing(),
                            ]);
    }

    /**
    * @Route("/prices", name="prices")
    */
    public function prices(Prices $price)
    {
        return $this->render('ticketing/prices.html.twig',
                            [
                                'normal' => $price->standard(),
                                'reduit' => $price->half(),
                            ]);
    }

    /**
    * @Route("/ticketing/form", name="visitor")
    * @Route("/ticketing/{id}/edit", name="edit")
    */

    public function visitorForm(Visitor $visitor = null, Request $request, ObjectManager $manager)
    {
        if(!$visitor)
        {
            $visitor = new Visitor();
        }            

        $form = $this->createForm(VisitorType::class, $visitor);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $manager->persist($visitor);
            $manager->flush();

            return $this->redirectToRoute('summary', ['id' => $visitor->getId()]);
        }

        return $this->render('ticketing/visitor.html.twig', ['formVisitor' => $form->createView(), 'editMode' => $visitor->getId() !== null
        ]);
    }

    /**
    * @Route("/ticketing/summary", name="summary")
    */
    public function summary(VisitorRepository $repo)
    {
        $visitors = $repo->findBy(array(), array('id' => 'desc'),1,0);

        return $this->render('ticketing/summary.html.twig', ['visitors' => $visitors]);
    }

    /**
    * @Route("/ticketing/payment", name="payment")
    */
    public function payment()
    {
        return $this->render('ticketing/payment.html.twig');
    }
}