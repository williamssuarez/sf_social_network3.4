<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

//ENTIDADES
use App\Entity\User;
use App\Entity\Publication;
use App\Entity\Like;

//SERVICIOS
use Knp\Component\Pager\PaginatorInterface;
use App\Services\NotificationService; //SERVICIO DE NOTIFICACION PERSONALIZADO POR WILLIAMS SUAREZ

class LikeController extends AbstractController
{
    private $notification;

    public function __construct(NotificationService $notification)
    {
        $this->notification = $notification;
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

            //CREANDO LA NOTIFICACION DEL LIKE
            $notif = $this->notification->set($publication->getUser(), 'like', $user->getId(), $publication->getId());
            //MENSAJE
            $status = 'Te ha gustado esta publicacion !!';
        } else {
            $status = 'Ha ocurrido un error y no se ha podido guardar el me gusta';
        }

        return new Response($status);
    }

    //METODO DE UNLIKE POR AJAX
    public function unlike( $id = null): Response
    {
        $user = $this->getUser();

        $em = $this->getDoctrine()->getManager();

        $like_repo = $em->getRepository(\App\Entity\Like::class);
        $like = $like_repo->findOneBy(array(
            "user" => $user,
            "publication" => $id
        ));

        $em->remove($like);
        $flush = $em->flush();

        if($flush == null){
            $status = 'Ya no te gusta esta publicacion';
        } else {
            $status = 'Ha ocurrido un error y no se ha podido desmarcar esta publicacion';
        }

        return new Response($status);
    }

    public function likes(Request $request, $nickname = null, PaginatorInterface $paginator): Response
    {
        $em = $this->getDoctrine()->getManager();

        //OBTENEMOS EL USUARIO DEL REPOSITORIO DE USUARIOS
        if($nickname != null){
            //SI EL USUARIO NOS LLEGA POR URL LO SACAMOS DEL REPOSITORIO
            $user_repo = $em->getRepository(\App\Entity\User::class);
            $user = $user_repo->findOneBy(array('nick' => $nickname));
        } else {
            //CASO CONTRARIO SI NO LLEGA POR LA URL LO SACAMOS DEL USUARIO LOGEADO
            $user = $this->getUser();
        }

        //SI NO SE ENCUENTRA REDIRIGIMOS A LA HOME
        if(empty($user) || !is_object($user)){
            return $this->redirect('home');
        }

        $user_id = $user->getId();
        $dql = "SELECT l FROM App\Entity\Like l WHERE l.user = $user_id ORDER BY l.id DESC";
        $query = $em->createQuery($dql);

        $likes = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            5
        );

        return $this->render('like/likes.html.twig', array(
            'profile_user' => $user,
            'pagination' => $likes
        ));
    }
}
