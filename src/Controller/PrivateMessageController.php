<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PrivateMessageController extends AbstractController
{

    public function index(): Response
    {
        return $this->render('private_message/index.html.twig', [
            'controller_name' => 'PrivateMessageController',
        ]);
    }
}
