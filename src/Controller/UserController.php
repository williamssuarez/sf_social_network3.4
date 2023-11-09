<?php

namespace App\Controller;

use App\Form\RegisterType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\User;

//SERVICIOS
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;


class UserController extends AbstractController
{

    private $encoderFactory;
    private $session;
    private $authenticationUtils;

    public function __construct(EncoderFactoryInterface $encoderFactory)
    {
        $this->encoderFactory = $encoderFactory;
        $this->session = new Session();
        $this->authenticationUtils = new AuthenticationUtils();
    }

    public function index(): Response
    {
        return $this->render('user/index.html.twig', [
            'controller_name' => 'UserController',
        ]);
    }

    public function login(Request $request): Response{

        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('user/login.html.twig', array(
            'last_username' => $lastUsername,
            'error' => $error
        ));

    }

    //CONTROLADOR PARA REGISTRAR USUARIOS
    public function register(Request $request): Response{

        //IMPORTANDO LA ENTIDAD USUARIOS
        $user = new User();
        //GENERANDO EL FORMULARIO Y GUARDANDOLO EN $FORM
        $form = $this->createForm(RegisterType::class, $user, [
            'empty_data' => new User(),
        ]);

        //MANEJANDO LA RESPUESTA DEL FORMULARIO
        $form->handleRequest($request);

        //SI RECIBIMOS EL FORMULARIO
        if ($form->isSubmitted() ){

            //SI ES VALIDO EL FORM
            if ($form->isValid()){

                //OBTENER DOCTRINE Y EL ENTITY MANAGER
                $em = $this->getDoctrine()->getManager();

                //USAR EL QUERY BUILDER PARA CONSTRUIR LA QUERY
                $query = $em->createQuery('SELECT u FROM App\Entity\User u WHERE u.email = :email OR u.nick = :nick')
                            ->setParameter('email', $form->get("email")->getData())
                            ->setParameter('nick', $form->get("nick")->getData());

                //OBTENER EL RESULTADO
                $user_isset = $query->getResult();

                //SI LA VARIABLE ESTA VACIA ES QUE NO EXISTE EL EMAIL O EL NICK
                if (count($user_isset) == 0){

                    //OBTENIENDO EL SERVICIO DE ENCRIPTACION
                    $encoder = $this->encoderFactory->getEncoder($user);

                    //ENCRIPTANDO LA CLAVE
                    $password = $encoder->encodePassword($form->get("password")->getData(), $user->getSalt());

                    //PREPARANDO LOS DATOS
                    $user->setPassword($password);
                    $user->setRole("ROLE_USER");
                    $user->setImage(null);

                    //SUBIENDO A LA BASE DE DATOS
                    $em->persist($user);
                    $flush = $em->flush();

                    //COMPROBANDO SI FUE SUBIDO CORRECTAMENTE
                    if ($flush == null){

                        $status = "Te has registrado correctamente";

                        //REDIRIGIENDO CON MENSAJE
                        $this->session->getFlashBag()->add("status", $status);
                        return $this->redirect("login");

                    } else {
                        $status = "No te has registrado correctamente";
                    }
                }
                //EMAIL O NICK YA EXISTEN
                else {
                    $status = "Este usuario o email ya existen";
                }



            }
            //SI NO ES VALIDO
            else {

                $status = "No te has registrado correctamente";

            }
            $this->session->getFlashBag()->add("status", $status);
        }


        //RENDERIZANDO EL FORM
        return $this->render('user/register.html.twig', array(
            "form" => $form->createView()
        ));

    }

    public function countUsersWithNick($nick)
    {
        $em = $this->getDoctrine()->getManager();
        $user_repo = $em->getRepository(\App\Entity\User::class);


    }

    public function nickTest(Request $request): Response {

        //OBTENIENDO EL VALOR NICK DEL FORMULARIO CON EL OBJETO REQUEST
        $nick = $request->get("nick");
        //$nick = "admin";

        //OBTENIENDO EL ENTITY MANAGER PARA CONSULTAS
        $em = $this->getDoctrine()->getManager();

        //OBTENIENDO EL REPOSITORIO
        $user_repo = $em->getRepository(\App\Entity\User::class);
        /*$query = $user_repo->createQueryBuilder('u')
            ->where('u.nick = :nick')
            ->setParameter('nick', $nick)
            ->getQuery();

        $result_query =  $query->getResult();*/
        $user_isset = $user_repo->findOneBy(array("nick" => $nick));

        /*var_dump($result_query);
        die();*/

        $result = $user_isset ? "used" : "unused";

        return new Response($result);
    }
}
