<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\User;

//FORMULARIOS
use App\Form\RegisterType;
use App\Form\UserType;

//SERVICIOS
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Knp\Component\Pager\PaginatorInterface;


class UserController extends AbstractController
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

    public function login(Request $request): Response{

        //COMPROBANDO SI EL USUARIO ESTA LOGEADO
        if(is_object($this->getUser())){
            return $this->redirect('home');
        }

        //USANDO SERVICIO PARA OBTENER EL ERROR Y EL ULTIMO USERNAME QUE INTENTO INGRESAR
        $error = $this->authenticationUtilss->getLastAuthenticationError();
        $lastUsername = $this->authenticationUtilss->getLastUsername();

        return $this->render('user/login.html.twig', array(
            'last_username' => $lastUsername,
            'error' => $error
        ));

    }

    //CONTROLADOR PARA REGISTRAR USUARIOS
    public function register(Request $request): Response{

        //COMPROBANDO SI EL USUARIO YA ESTA LOGEADO
        if(is_object($this->getUser())){
            return $this->redirect('home');
        }

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


    //SOLICITUD AJAX DEL NICK-TEST.JS
    public function nickTest(Request $request): Response {

        //OBTENIENDO EL VALOR NICK DEL FORMULARIO CON EL OBJETO REQUEST
        $nick = $request->get("nick");
        //$nick = "admin";

        //OBTENIENDO EL ENTITY MANAGER PARA CONSULTAS
        $em = $this->getDoctrine()->getManager();

        //OBTENIENDO EL REPOSITORIO
        $user_repo = $em->getRepository(\App\Entity\User::class);

        $user_isset = $user_repo->findOneBy(array("nick" => $nick));

        $result = $user_isset ? "used" : "unused";

        return new Response($result);
    }

    //EDITAR DATOS DE USUARIO
    public function editUser(Request $request): Response{

        $user = $this->getUser();

        if ($user){

            $user_image = $user->getImage();
            $form = $this->createForm(UserType::class, $user);

        } else {
            return $this->redirect("login");
        }

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
                if (count($user_isset) == 0 || ($user->getEmail() == $user_isset[0]->getEmail() && $user->getNick() == $user_isset[0]->getNick())){

                    //SUBIENDO IMAGEN
                    $file = $form["image"]->getData();

                    if (!empty($file) && $file != null){

                        $ext = $file->guessExtension();
                        if ($ext == 'jpg' || $ext == 'jpeg' || $ext == 'png' || $ext == 'gif'){

                            $file_name = $user->getId().time().'.'.$ext;
                            $file->move("uploads/users", $file_name);

                            $user->setImage($file_name);
                        }
                    } else {
                        $user->setImage($user_image);
                    }

                    //SUBIENDO A LA BASE DE DATOS
                    $em->persist($user);
                    $flush = $em->flush();

                    //COMPROBANDO SI FUE SUBIDO CORRECTAMENTE
                    if ($flush == null){

                        $status = "Has modificado tus datos correctamente";

                    } else {
                        $status = "No has actualizado tus datos correctamente";
                    }
                }
                //EMAIL O NICK YA EXISTEN
                else {
                    $status = "Este usuario o email ya existen";
                }

            }
            //SI NO ES VALIDO
            else {

                $status = "No se han actualizado tus datos correctamente";

            }
            $this->session->getFlashBag()->add("status", $status);
            return $this->redirect('my-data');
        }

        return $this->render('user/edit_user.html.twig', array(
            "form" => $form->createView()
        ));

    }

    //PARA LISTAR A LOS USUARIOS EN LA RUTA PEOPLE
    public function users(Request $request): Response {

        //OBTENIENDO DOCTRINE
        $em = $this->getDoctrine()->getManager();

        $dql = "SELECT u FROM App\Entity\User u ORDER BY u.id ASC";
        $query = $em->createQuery($dql);

        //$paginator = $this->get('knp_paginator');
        $pagination = $this->paginator->paginate(
            $query, $request->query->getInt('page', 1), 5
        );

        return $this->render('user/users.html.twig', array(
            'pagination' => $pagination
        ));
    }

    //PARA BUSCAR A LOS USUARIOS EN LA RUTA SEARCH
    public function search(Request $request): Response {

        //OBTENIENDO DOCTRINE
        $em = $this->getDoctrine()->getManager();

        //OBTENIENDO LO INSERTADO EN EL FORMULARIO
        $search = trim($request->query->get("search", null));

        if($search == null){
            return $this->redirect($this->generateUrl('app_homepage'));
        }

        $dql = "SELECT u FROM App\Entity\User u 
                WHERE u.name LIKE :search 
                OR u.surname LIKE :search 
                OR u.nick LIKE :search ORDER BY u.id ASC";
        $query = $em->createQuery($dql)->setParameter('search', "%$search%") ;

        //$paginator = $this->get('knp_paginator');
        $pagination = $this->paginator->paginate(
            $query, $request->query->getInt('page', 1), 5
        );

        return $this->render('user/users.html.twig', array(
            'pagination' => $pagination
        ));
    }

    public function profile(Request $request, $nickname = null){
        $em = $this->getDoctrine()->getManager();

        if($nickname != null){
            $user_repo = $em->getRepository(\App\Entity\User::class);
            $user = $user_repo->findOneBy(array('nick' => $nickname));
        } else {
            $user = $this->getUser();
        }

        if(empty($user) || !is_object($user)){
            return $this->redirect('home');
        }

        $user_id = $user->getId();
        $dql = "SELECT p FROM App\Entity\Publication p WHERE p.user = $user_id ORDER BY p.id DESC";
        $query = $em->createQuery($dql);

        $publications = $this->paginator->paginate(
          $query,
          $request->query->getInt('page', 1),
            5
        );

        return $this->render('user/profile.html.twig', array(
            'user' => $user,
            'pagination' => $publications
        ));
    }
}
