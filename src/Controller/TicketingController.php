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
use Doctrine\ORM\EntityManagerInterface;
use App\Service\Schedule;
use App\Service\Prices;
use App\Service\Ages;
use App\Service\Summary;
use Ramsey\Uuid\Uuid;

class TicketingController extends AbstractController
{
    /**
     * @Route("/ticketing", name="ticketing")
     */
    public function index(Schedule $schedule)
    {
        return $this->render('ticketing/index.html.twig', [
            'controller_name' => 'TicketingController',
            'closed_day' => [0,2],
            'closeHourTickets'=> $schedule->getClosedHourTickets(),
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
                                'closeHourTickets'=> $schedule->getClosedHourTickets(),
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
     * @Route("/ticketing", name="ticketing")
     */    
    public function ChoiceForm(Choice $choice = null, Request $request, ObjectManager $manager, Schedule $schedule)
    {
        
        if(!$choice)
        {
            $choice = new Choice();
        }            
      
        $form = $this->createForm(ChoiceType::class, $choice);
       
        $form->handleRequest($request);
   
        $choice->setUuid(Uuid::uuid4());

       
        if($form->isSubmitted() && $form->isValid())
        {
            $manager->persist($choice);
            $manager->flush();

            //return $this->redirectToRoute('visitor', ['id' => $choice->getId()]);
            return $this->redirect("/ticketing/". $choice->getId()."/edit");
        }

        return $this->render('ticketing/index.html.twig', ['halfTicketsMaxHour' => $schedule->getHalfTicketsMaxHour(),'closeHourTickets'=> $schedule->getClosedHourTickets(),'formChoice' => $form->createView(), 'editMode' => $choice->getId() !== null
        ]);
    }    

    /**
    * @Route("/ticketing/form", name="visitor")
    * @Route("/ticketing/{id}/edit", name="edit")
    */
    public function visitorForm(Choice $choice = null, Request $request, ObjectManager $manager,EntityManagerInterface $em)
    {
        //Get the choice form id
        $currentId = $request->attributes->get('id');
        $currentChoice = new Choice();
        //Get current choice
        $repository = $em->getRepository(Choice::class);
        $currentChoice = $repository->findOneBy(['id' => $currentId]);
        if($currentChoice->getVisitors()->count()==0)
        {
          

            //If there is no choice in database linked to the id
            if (!$currentChoice) {
                throw $this->createNotFoundException(sprintf('No Tickets for id "%s"', $currentId));
            }
            //It's a new choice, we need to create the visitor
            
            for ($i = 1; $i <= $currentChoice->getTickets(); $i++) {
                $visitor = new Visitor();
                $visitor->setName("Nom");
                $visitor->setFirstName("Prenom");
                $visitor->setCountry("Pays");
                $visitor->setBirthday(New \DateTime());
                $visitor->setChoiceuuid($currentChoice->getUuid());
                $choice->addVisitor($visitor);
                $manager->persist($visitor);
                $manager->persist($choice);
                $manager->flush();
            }

 
        }      

        $form = $this->createForm(ChoiceType::class, $choice);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $manager->persist($choice);
            $manager->flush();

            return $this->redirectToRoute('summary', ['id' => $currentId]);
        }

       
        return $this->render('ticketing/visitor.html.twig', ['currentChoice'=>$currentChoice, 'formVisitor' => $form->createView(), 'editMode' => $currentId !== null
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