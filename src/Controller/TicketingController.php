<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
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
    public function ChoiceForm(SessionInterface $session,EntityManagerInterface $em, Choice $choice = null, Request $request, ObjectManager $manager, Schedule $schedule)
    {

        //error_reporting(E_ALL);
        //ini_set("display_errors", 1);

        //init du formulaire
        if(!$choice)
        {
            $choice = new Choice();
        }            
        //create forms
        $form = $this->createForm(ChoiceType::class, $choice);
       
        $form->handleRequest($request);
   
        //create futur uuid
        $choice->setUuid(Uuid::uuid4());

       

        //prepare to filter date when more than 1 000 tickets
        //SQL : SELECT `visit`, SUM(`tickets`) as totaltickets FROM `choice` where visit > DATE_SUB(curdate(), INTERVAL 2 DAY) group by `visit` HAVING SUM(tickets) >= 1000
        $rawSql = " SELECT `visit`, SUM(`tickets`) as totaltickets FROM `choice` where visit > DATE_SUB(curdate(), INTERVAL 2 DAY) group by `visit` HAVING SUM(tickets) >= 1000";

        $stmt = $em->getConnection()->prepare($rawSql);
        $stmt->execute([]);
    
        $result = $stmt->fetchAll();
        $datesSoldOut = array();
        
        //Create a array of sold out date and use it for calendar and test if possible to buy the number of tickets
        $canBuyTickets = true;
        $canBuyTicketsAmount = 1000;
        foreach ( $result as $visit){
            array_push($datesSoldOut,$visit["visit"]);
        }


        //prepare sql to get total amount of ticket for the selected date
        if($choice->getVisit() != null)
        {
            $rawSql = "SELECT `visit`, SUM(`tickets`) as totaltickets FROM `choice` where date(visit) = date('".$choice->getVisit()->format('Y-m-d')."') group by `visit`";
           
     
            $stmt = $em->getConnection()->prepare($rawSql);
            $stmt->execute([]);
        
            $result = $stmt->fetchAll();
                  
            foreach ( $result as $visit){
                if($choice->getVisit()->format('Y-m-d') == $visit["visit"]) // for the selected date
                {
                    //check if under 1000
                    if(($visit["totaltickets"]+$choice->getTickets())>1000)
                    {
                        //can't take the amount
                        $canBuyTickets = false;
                        //display the max amount for the user
                        $canBuyTicketsAmount = 1000 - $visit["totaltickets"];
                    } 
                }
            }
        }
        


        if($form->isSubmitted() && $form->isValid())
        {

            //test if choice number of ticket is ok
           if($canBuyTickets)
            {
                //choice is ok : persist it
                
                $manager->persist($choice);
                $manager->flush();
                $session->set('currentChoiceID', $choice->getId());
     
                return $this->redirect("/ticketing/". $choice->getId()."/edit");
            }
        }
        

        /*
				var parts ='{{dateSolddout}}'.split('-');
				// Please pay attention to the month (parts[1]); JavaScript counts months from 0:
				// January - 0, February - 1, etc.
				var mydate = new Date(parts[0], parts[1] - 1, parts[2]); 
        */
       
        return $this->render('ticketing/index.html.twig', ['canBuyTicketsAmount'=>$canBuyTicketsAmount,'canBuyTickets'=> $canBuyTickets,'datesSoldOut'=>$datesSoldOut,'halfTicketsMaxHour' => $schedule->getHalfTicketsMaxHour(),'closeHourTickets'=> $schedule->getClosedHourTickets(),'formChoice' => $form->createView(), 'editMode' => $choice->getId() !== null
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
    public function visitorForm(SessionInterface $session,Choice $choice = null, Request $request, ObjectManager $manager,Prices $price,Ages $ages,EntityManagerInterface $em)
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
        //if there is no adult (baby only, refuse the validation)
        $AdultPresent = true;
        if($form->isSubmitted() && $form->isValid())
        {
            //Because field is disable for display, we get
            $choice->setTickets($currentNumnerOfTickets);
            $manager->persist($choice);
            $manager->flush();


            $summary = new summary();
            $summary->loadChoice($currentChoice,$price,$ages);
            //if there is no adult (baby only, refuse the validation)
            $AdultPresent = false;
            foreach ($summary->getTicketcollection() as $ticket){
                if($ticket->getTarifType() == "adult" || $ticket->getTarifType() == "senior")
                 $AdultPresent = true;
            }
            if($AdultPresent)
                 return $this->redirect("/ticketing/". $currentId."/summary");
            else
                 return $this->render('ticketing/visitor.html.twig', ['isnew'=>$isnew,'AdultPresent'=> $AdultPresent, 'NumberOfTickets'=>$currentNumnerOfTickets, 'currentChoice'=>$currentChoice, 'formVisitor' => $form->createView(), 'editMode' => $currentId !== null]);
            
            //return $this->redirectToRoute('summary', ['id' => $currentId]);
        }

       
        return $this->render('ticketing/visitor.html.twig', ['isnew'=>$isnew,'AdultPresent'=> $AdultPresent,'currentChoice'=>$currentChoice, 'formVisitor' => $form->createView(), 'editMode' => $currentId !== null
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
    * @Route("/ticketing/payment", name="paymentempty")
    * @Route("/ticketing/{id}/payment", name="payment")
    * @Route("/ticketing/{id}/{result}/payment", name="paymentresult")
    * @Route("/ticketing/{id}/{result}/payment/{sessionid}", name="paymentsuccess")
    */
    public function payment(SessionInterface $session,EntityManagerInterface $em,Request $request,\Swift_Mailer $mailer,UrlGeneratorInterface $router,Prices $price,Ages $ages)
    {
        //Get the choice form id
        $currentId = $request->attributes->get('id');
        //get the checkout type
        $checkoutType = $request->attributes->get('result');

        //test if is empty and need to pay
        if(!isset($checkoutType) )
        {
            $checkoutType = "ToPay";
        }

        if(empty ($checkoutType))
        {
            $checkoutType = "ToPay";
        }

        //Test if user can read the item
        $sessionChoiceId = $session->get('currentChoiceID');    
        if($sessionChoiceId != $currentId)
        {
            return $this->redirect("/ErrorTickkets");
        }
        //get the choice
        $repository = $em->getRepository(Choice::class);
        $currentChoice = $repository->findOneBy(['id' => $currentId]);

        $summary = new summary();
        $summary->loadChoice($currentChoice,$price,$ages);

        //If there is no choice in database linked to the id
        if (!$currentChoice) {
            throw $this->createNotFoundException(sprintf('No Tickets for id "%s"', $currentId));
        }

      //case user need to pay
      if($checkoutType == "ToPay")
      {

        //create description of the invoice
        $description = "".$currentChoice->getTickets();
        if($currentChoice->getTickets()>1)
            $description.=" billets ";
        else
            $description.=" billet ";

        if($currentChoice->getHalfDay())
            $description.="demi-journée ";
        else
            $description.=" journée ";


        //create return url for stripe
        $urlresultsucces =  $router->generate('paymentresult' , ['id' => $sessionChoiceId,'result' => 'success'],UrlGeneratorInterface::ABSOLUTE_URL); 
        $urlresultcancel =  $router->generate('paymentresult' , ['id' => $sessionChoiceId,'result' => 'cancel'],UrlGeneratorInterface::ABSOLUTE_URL);
    

        //init stripe
        \Stripe\Stripe::setApiKey('sk_test_ASRZDPsMNfDcCqJbjLd0A4IS00E6ftdDs6');
        //Create checkout
        $sessionStripe = \Stripe\Checkout\Session::create([
            'payment_method_types' => ['card'],
            'line_items' => [[
              'name' => 'Réservation visite du louvre',
              'description' => $description,
              'images' => ['https://tourscanner.co/blog/wp-content/uploads/2018/09/Louvre-museum-tickets.jpg'],
              'amount' => $summary->gettotalPrices()*100,
              'currency' => 'eur',
              'quantity' => 1,
             
            ]],
            'success_url' =>  $urlresultsucces,
            'cancel_url' => $urlresultcancel,
            "client_reference_id" => $currentChoice->getUuid(),
          ]);

          //update the choice with the user id
          $currentChoice->setStripeid($sessionStripe->id);
          $em->persist($currentChoice);
          $em->flush();
          
          return $this->render('ticketing/payment.html.twig' , ['CHECKOUT_SESSION_ID' => $sessionStripe->id,'checkoutType'=> $checkoutType]);
       }

       //case paiement success
       if($checkoutType == "success")
       {
            //init stripe
        \Stripe\Stripe::setApiKey('sk_test_ASRZDPsMNfDcCqJbjLd0A4IS00E6ftdDs6');
        //get the session id for the choice
        $userSessionID = $currentChoice->getStripeid();
        $sessionStripe =\Stripe\Checkout\Session::retrieve($userSessionID);
        $UserMail="";
        if(isset($sessionStripe) && !empty($sessionStripe))
        {
            //get the payment intention
            $payment_intent_id =  $sessionStripe->payment_intent;
            $payment_intent = \Stripe\PaymentIntent::retrieve($payment_intent_id);
            //get the payment status
            $payementStatus = $payment_intent->status;
            if($payementStatus == "succeeded")
            {
                //get the customer
                $customerid = $payment_intent->customer;
                $customer = \Stripe\Customer::retrieve($customerid);
                //get the customer mail
                $UserMail =  $customer->email;
                //send email
                $message = (new \Swift_Message('Billeterie du Louvre'))
                ->setFrom('billetries@projet4OpenClassRoom.com')
                ->setTo($UserMail)
                ->setBody(
                    $this->renderView(
                        'emails/receipt.html.twig',
                        ['summary' => $summary]
                    ),
                    'text/html'
                )
                ;
                

                $resultMail = $mailer->send($message,$failures);
                //Uncomment to test mail render
               // return $this->render('emails/receipt.html.twig',['summary' => $summary]);
                //render page result
                return $this->render('ticketing/payment.html.twig' , ['summary' => $summary,'resultMail'=>$resultMail,'checkoutType'=> $checkoutType,'usermail'=>$UserMail,'userSessionID'=> $userSessionID]);
            }
            else
            {
                //payement not payed
                return $this->render('ticketing/payment.html.twig' , ['summary' => $summary,'checkoutType'=> 'NotPayed','usermail'=>$UserMail,'userSessionID'=> $userSessionID]);
            }
        }
        else
        {
            //case payment not already payed 
            return $this->render('ticketing/payment.html.twig' , ['summary' => $summary,'checkoutType'=> 'NotPayed','usermail'=>$UserMail,'userSessionID'=> $userSessionID]);
        }
          
       }

       //case paiment aborted
       if($checkoutType == "cancel")
       {
          return $this->render('ticketing/payment.html.twig' , ['summary' => $summary,'checkoutType'=> $checkoutType]);  
       }
    }

    
}