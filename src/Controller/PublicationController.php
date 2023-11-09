<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PublicationController extends AbstractController
{

    public function index(): Response
    {


        echo "Publications Index";
        die();

        return $this->render('publication/index.html.twig', [
            'controller_name' => 'PublicationController',
        ]);
    }
}
