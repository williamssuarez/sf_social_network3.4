<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\User;

class InicioController extends AbstractController
{
    public function index(): Response
    {

        $em = $this->getDoctrine()->getManager();

        $user_repo = $em->getRepository(User::class);

        $user = $user_repo->find(1);

        echo "Bienvenido ".$user->getName()." ".$user->getSurname()."<hr />";

        var_dump($user);
        die();

        return $this->render('inicio/index.html.twig', [
            'controller_name' => 'InicioController',
        ]);
    }
}
