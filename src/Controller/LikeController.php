<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

//ENTIDADES
use App\Entity\User;
use App\Entity\Publication;
use App\Entity\Like;

class LikeController extends AbstractController
{

    public function index(): Response
    {
        return $this->render('like/index.html.twig', [
            'controller_name' => 'LikeController',
        ]);
    }

    //METODO PARA LIKE POR AJAX
    public function like($id = null): Response
    {
        $user = $this->getUser();

        $em = $this->getDoctrine()->getManager();

        $publication_repo = $em->getRepository(\App\Entity\Publication::class);
        $publication = $publication_repo->find($id);

        $like = new Like();
        $like->setUser($user);
        $like->setPublication($publication);

        $em->persist($like);
        $flush = $em->flush();

        if($flush == null){
           $status = 'Te ha gustado esta publicacion !!';
        } else {
            $status = 'Ha ocurrido un error y no se ha podido guardar el me gusta';
        }

        return new Response($status);

    }
}
