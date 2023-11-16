<?php

namespace App\Controller;

//use http\Env\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

//ENTIDADES
use App\Entity\Following;
use App\Entity\User;


//SERVICIOS
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Knp\Component\Pager\PaginatorInterface;

class FollowingController extends AbstractController
{

    //VARIABLES PARA LOS SERVICIOS
    private $encoderFactory; //SERVICIO DE ENCRIPTACION
    private $session; //SERVICIO DE SESION
    private $authenticationUtilss; //SERVICIO DE AUTENTICACION
    private $paginator; //SERVICIO DE PAGINACION KNP

    public function __construct(EncoderFactoryInterface $encoderFactory,
                                SessionInterface $session,
                                RequestStack $requestStack,
                                PaginatorInterface $paginator)
    {
        $this->encoderFactory = $encoderFactory;
        $this->session = $session;
        $this->authenticationUtilss = new AuthenticationUtils($requestStack);
        $this->paginator = $paginator;
    }

    public function index(): Response
    {
        return $this->render('following/index.html.twig', [
            'controller_name' => 'FollowingController',
        ]);
    }

    //FOLLOWING CON AJAX
    public function follow(Request $request) : Response {

        $user = $this->getUser();
        $followed_id = $request->get('followed');

        $em = $this->getDoctrine()->getManager();

        $user_repo = $em->getRepository('App\Entity\User');
        $followed = $user_repo->find($followed_id);

        $following = new Following();
        $following->setUser($user);
        $following->setFollowed($followed);

        $em->persist($following);
        $flush = $em->flush();

        if ($flush == null){
            $status = "Ahora estas siguiendo a este usuario !! ";
        } else {
            $status = "No se ha podido seguir a este usuario, intente en otro momento !!";
        }

        return new Response($status);

    }

    //UNFOLLOW CON AJAX
    public function unfollow(Request $request) : Response {

        $user = $this->getUser();
        $followed_id = $request->get('followed');

        $em = $this->getDoctrine()->getManager();

        $following_repo = $em->getRepository('App\Entity\Following');
        $followed = $following_repo->findOneBy(array(
            'user' => $user,
            'followed' => $followed_id
        ));


        $em->remove($followed);
        $flush = $em->flush();

        if ($flush == null){
            $status = "Has dejado de seguir a este usuario !! ";
        } else {
            $status = "No se ha podido dejar de seguir a este usuario, intente en otro momento !!";
        }

        return new Response($status);

    }
}
