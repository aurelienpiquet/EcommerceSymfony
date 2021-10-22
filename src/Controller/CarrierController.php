<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CarrierController extends AbstractController
{
    /**
     * @Route("/carrier", name="carrier")
     */
    public function index(): Response
    {
        return $this->render('carrier/index.html.twig', [
            'controller_name' => 'CarrierController',
        ]);
    }
}
