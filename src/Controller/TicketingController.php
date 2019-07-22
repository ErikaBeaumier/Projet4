<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Visitor;
use App\Repository\VisitorRepository;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Form\FormType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

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
    * @Route("/ticketing/form", name="visitor")
    */

    public function visitorForm(Request $request, ObjectManager $manager)
    {
        $visitor = new Visitor();

        $form = $this->createFormBuilder($visitor)
                     ->add('name')
                     ->add('firstName')
                     ->add('birthday')
                     ->add('country')
                     ->add('reduc')
                     ->getForm();

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $manager->persist($visitor);
            $manager->flush();

            return $this->redirectToRoute('summary', ['id' => $visitor->getId()]);
        }

        return $this->render('ticketing/visitor.html.twig', ['formVisitor' => $form->createView()
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
}