<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\Disabled;

class DateController extends AbstractController
{
    /**
     * @Route("/date/add", name="add_date")
     */
    public function add(Disabled $disabled)
    {
        $days_disabled = $message->dateDisabled('02');
        $this->addFlash('New Day Off', $days_disabled);
        return $this->render('date/index.html.twig', [
            'controller_name' => 'DateController',
        ]);
    }
}
