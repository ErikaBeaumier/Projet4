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
use Symfony\Component\HttpFoundation\Session\SessionInterface;

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
    public function ChoiceForm(SessionInterface $session, Choice $choice = null, Request $request, ObjectManager $manager, Schedule $schedule)
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
            $session->set('currentChoiceID', $choice->getId());
            //return $this->redirectToRoute('visitor', ['id' => $choice->getId()]);
            return $this->redirect("/ticketing/". $choice->getId()."/edit");
        }

        return $this->render('ticketing/index.html.twig', ['halfTicketsMaxHour' => $schedule->getHalfTicketsMaxHour(),'closeHourTickets'=> $schedule->getClosedHourTickets(),'formChoice' => $form->createView(), 'editMode' => $choice->getId() !== null
        ]);
    }    

     /**
     * @Route("/ErrorTickkets", name="ErrorTickkets")
     */    
    public function ErrorTickketsForm()
    {
        return $this->render("ticketing/errorTicket.html.twig");
    }

    /**
    * @Route("/ticketing/form", name="visitor")
    * @Route("/ticketing/{id}/edit", name="edit")
    */
    public function visitorForm(SessionInterface $session,Choice $choice = null, Request $request, ObjectManager $manager,EntityManagerInterface $em)
    {
        //Get the choice form id
        $currentId = $request->attributes->get('id');
        //Test if user can read the item
        $sessionChoiceId = $session->get('currentChoiceID');    
        if($sessionChoiceId != $currentId)
        {
           return $this->redirect("/ErrorTickkets");
        }
        $currentChoice = new Choice();
       
        //Get current choice
        $repository = $em->getRepository(Choice::class);
        $currentChoice = $repository->findOneBy(['id' => $currentId]);
        //If there is no choice in database linked to the id
        if (!$currentChoice) {
            throw $this->createNotFoundException(sprintf('No Tickets for id "%s"', $currentId));
        }
        $currentNumnerOfTickets = $currentChoice->getTickets();
        //test is new : used in twig to display or not data in field
        $isnew = false;
        if($currentChoice->getVisitors()->count()==0)
        {

            //It's a new choice, we need to create the visitor
            $isnew = true;

            //create the fields for the numbers of tickets
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
            //Because field is disable for display, we get
          
            $choice->setTickets($currentNumnerOfTickets);
            $manager->persist($choice);
            $manager->flush();

            return $this->redirect("/ticketing/". $currentId."/summary");
            //return $this->redirectToRoute('summary', ['id' => $currentId]);
        }

       
        return $this->render('ticketing/visitor.html.twig', ['isnew'=>$isnew,'currentChoice'=>$currentChoice, 'formVisitor' => $form->createView(), 'editMode' => $currentId !== null
        ]);
    }

    /**
    * @Route("/ticketing/summary", name="summary")
    * * @Route("/ticketing/{id}/summary", name="summaryEdit")
    */
    public function summary(SessionInterface $session,Prices $price,Ages $ages,Request $request,EntityManagerInterface $em)
    {
        //Get the choice form id
        $currentId = $request->attributes->get('id');

        //Test if user can read the item
        $sessionChoiceId = $session->get('currentChoiceID');    
        if($sessionChoiceId != $currentId)
        {
           return $this->redirect("/ErrorTickkets");
        }

        $repository = $em->getRepository(Choice::class);
        $currentChoice = $repository->findOneBy(['id' => $currentId]);

        //If there is no choice in database linked to the id
        if (!$currentChoice) {
            throw $this->createNotFoundException(sprintf('No Tickets for id "%s"', $currentId));
        }

        $summary = new summary();
        $summary->loadChoice($currentChoice,$price,$ages);

        return $this->render('ticketing/summary.html.twig', ['summary' => $summary]);
    }

    /**
    * @Route("/ticketing/payment", name="payment")
    */
    public function payment()
    {
        return $this->render('ticketing/payment.html.twig');
    }
}