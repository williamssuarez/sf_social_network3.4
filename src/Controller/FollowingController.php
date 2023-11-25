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
use App\Services\NotificationService; //SERVICIO PERSONALIZADO

class FollowingController extends AbstractController
{

    //VARIABLES PARA LOS SERVICIOS
    private $encoderFactory; //SERVICIO DE ENCRIPTACION
    private $session; //SERVICIO DE SESION
    private $authenticationUtilss; //SERVICIO DE AUTENTICACION
    private $paginator; //SERVICIO DE PAGINACION KNP
    private $notifications;

    public function __construct(EncoderFactoryInterface $encoderFactory,
                                SessionInterface $session,
                                RequestStack $requestStack,
                                PaginatorInterface $paginator,
                                NotificationService $notification)
    {
        $this->encoderFactory = $encoderFactory;
        $this->session = $session;
        $this->authenticationUtilss = new AuthenticationUtils($requestStack);
        $this->paginator = $paginator;
        $this->notifications = $notification;
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
            //CREANDO NOTIFICACION
            $notifs = $this->notifications->set($followed, 'follow', $user->getId());
            //CREANDO MENSAJE
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

    public function following(Request $request, $nickname = null): Response
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
        $dql = "SELECT f FROM App\Entity\Following f WHERE f.user = $user_id ORDER BY f.id DESC";
        $query = $em->createQuery($dql);

        $following = $this->paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            5
        );

        return $this->render('following/follow.html.twig', array(
            'type' => 'following',
            'profile_user' => $user,
            'pagination' => $following
        ));
    }

    public function followed(Request $request, $nickname = null): Response
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
        $dql = "SELECT f FROM App\Entity\Following f WHERE f.followed = $user_id ORDER BY f.id DESC";
        $query = $em->createQuery($dql);

        $followed = $this->paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            5
        );

        return $this->render('following/follow.html.twig', array(
            'type' => 'followed',
            'profile_user' => $user,
            'pagination' => $followed
        ));
    }
}
