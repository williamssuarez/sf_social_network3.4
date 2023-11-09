<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class EndController extends AbstractController
{

    public function index(): Response
    {

        return $this->render('end/index.html.twig', [
            'controller_name' => 'EndController',
        ]);
    }
}
