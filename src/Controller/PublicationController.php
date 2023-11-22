<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

//ENTIDADES
use App\Entity\User;
use App\Entity\Publication;

//FORMULARIO
use App\Form\PublicationType;

//SERVICIOS
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Knp\Component\Pager\PaginatorInterface;

class PublicationController extends AbstractController
{

    private $session; //SERVICIO DE SESION
    private $paginator; //SERVICIO DE PAGINACION

    public function __construct(SessionInterface $session, PaginatorInterface $paginator)
    {
        $this->session = $session;
        $this->paginator = $paginator;
    }


    public function index(Request $request): Response
    {
        //ENTITY MANAGER Y DOCTRINE
        $em = $this->getDoctrine()->getManager();
        $publication = new Publication();

        //OBTENER USUARIO ACTUAL
        $user = $this->getUser();
        //CREANDO EL FORMULARIO
        $form = $this->createForm(PublicationType::class, $publication);

        //RECIBIENDO EL FORMULARIO
        $form->handleRequest($request);
        //SI ES ENVIADO:
        if ($form->isSubmitted()){
            //Y SI ES VALIDO
            if ($form->isValid()){

                //PRIMERO QUE NADA SUBIR LOS FICHEROS
                //SUBIR IMAGEN
                $file = $form['image']->getData();

                //SI LA IMAGEN NO ESTA VACIA
                if (!empty($file) && $file != null){

                    //OBTENIENDO LA EXTENSION
                    $ext = $file->guessExtension();

                    //SI LA EXTENSION ES ALGUNA DE ESTAS, ENTONCES ES VALIDA
                    if ($ext == 'jpeg' || $ext == 'jpg' || $ext == 'png' || $ext == 'gif'){

                        //COMPONIENDO EL NOMBRE DE LA IMAGEN CON LA ID DEL USUARIO, LA FECHA, Y LA EXTENSION
                        $file_name = $user->getId().time().".".$ext;
                        //MOVIENDO LA IMAGEN A ESE DIRECTORIO
                        $file->move("uploads/publications/images", $file_name);

                        //GUARDANDO EL NOMBRE DE LA IMAGEN EN LA BASE DE DATOS
                        $publication->setImage($file_name);

                    } else {
                        //CASO CONTRARIO, NO ES UNA EXTENSION VALIDA, SE QUEDA EN NULL
                        $publication->setImage(null);
                    }

                } else {
                    //SI LA IMAGEN ESTA VACIA SE QUEDA EN NULL
                    $publication->setImage(null);
                }

                //SUBIR DOCUMENTO
                $doc = $form['document']->getData();

                //SI EL DOCUMENTO NO ESTA VACIO NI ES NULO
                if (!empty($doc) && $doc != null){

                    //OBTENIENDO LA EXTENSION DEL DOCUMENTO
                    $ext = $doc->guessExtension();

                    //SI LA EXTENSION ES ALGUNA DE ESTAS, ENTONCES ES VALIDA
                    if ($ext == 'pdf' ||  $ext == 'txt' ||
                        $ext == 'xlsx' || $ext == 'xls' ||
                        $ext == 'docx' || $ext == 'doc' ||
                        $ext == 'pptx' || $ext == 'ppt' ||
                        $ext == 'rar' || $ext == 'zip'){

                        //COMPONIENDO EL NOMBRE DEL DOCUMENTO CON LA ID DEL USUARIO, LA FECHA, Y LA EXTENSION
                        $file_name = $user->getId().time().".".$ext;
                        //MOVIENDO EL DOCUMENTO A ESE DIRECTORIO
                        $doc->move("uploads/publications/documents", $file_name);

                        //GUARDANDO EL NOMBRE DEL DOCUMENTO EN LA BASE DE DATOS
                        $publication->setDocument($file_name);

                    } else {
                        //CASO CONTRARIO, NO ES UNA EXTENSION VALIDA, SE QUEDA EN NULL
                        $publication->setDocument(null);
                    }

                } else {
                    //SI EL DOCUMENTO ESTA VACIO O ES NULO SE QUEDA EN NULL
                    $publication->setDocument(null);
                }

                //SUBIENO LA INFO A LA BASE DE DATOS

                //PREPARANDO LOS DATOS RESTANTES
                $publication->setUser($user);
                $publication->setCreatedAt(new \DateTime("now"));

                //SUBIENDO A LA DB
                $em->persist($publication);
                $flush = $em->flush();

                //SI FLUSH ES NULL ENTONCES NO HAY NINGUN ERROR
                if ($flush == null) {

                    $status = 'La publicacion se ha creado correctamente';

                } else {
                    //CASO CONTRARIO, HA OCURRIDO UN ERROR
                    $status = 'Ha ocurrido un error al intentar añadir la publicacion';

                }

            } else {
                //SI NO ES VALIDO EL FORMULARIO
                $status = 'La publicacion no se ha creado, porque el formulario no es valido';
            }

            $this->session->getFlashBag()->add("status", $status);
            return $this->redirectToRoute("app_homepage");
        }

        $publications = $this->getPublications($request);

        //RENDERIZANDO EL FORMULARIO
        return $this->render('publication/home.html.twig', array(
            'form' => $form->createView(),
            'pagination' => $publications
        ));
    }

    //METODO AUXILIAR PARA OBTENER LAS PUBLICACIONES
    public function getPublications($request){

        //OBTENIENDO DOCTRINE Y EL USUARIO LOGEADO
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();

        //OBTENEMOS LOS REPOSITORIOS DE LAS PUBLICACIONES Y FOLLOWING
        $following_repo = $em->getRepository(\App\Entity\Following::class);
        $publication_repo = $em->getRepository(\App\Entity\Publication::class);

        //ESTA ES LA QUERY SQL PARA OBTENER TODAS LAS PUBLICACIONES DEL USUARIO LOGEADO O DE LOS OTROS QUE SIGUE
        /*SELECT * FROM publications WHERE user_id = 9
        OR user_id IN (SELECT followed FROM following WHERE `user` = 9)*/

        //OBTENIENDO LOS USUARIOS QUE SIGUE EL USER LOGEADO
        $following = $following_repo->findBy(array(
            'user' => $user
        ));

        //CREANDO UN ARRAY DE APOYO (ACA VAMOS A METER LOS RESULTADOS DE LA QUERY ANTERIOR)
        $following_array = array();

        //LLENANDO EL ARRAY CON LOS RESULTADOS DE LA QUERY
        foreach ($following as $follow){
            $following_array[] = $follow->getFollowed();
        }

        //HACIENDO LA QUERY DE LAS PUBLICACIONES
        $query = $publication_repo->createQueryBuilder('p')
                ->where('p.user = (:user_id) OR p.user IN (:following)')
                ->setParameter('user_id', $user->getId())
                ->setParameter('following', $following_array)
                ->orderBy('p.id', 'DESC')
                ->getQuery();

        //USANDO EL SERVICIO DE PAGINACION
        $pagination = $this->paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            5
        );

        return $pagination;

    }

    //ELIMINAR PUBLICACIONES CON AJAX
    public function removePublication(Request $request, $id): Response
    {
        $em = $this->getDoctrine()->getManager();
        $publication_repo = $em->getRepository(\App\Entity\Publication::class);
        $publication = $publication_repo->find($id);
        $user = $this->getUser();

        if($user->getId() == $publication->getUser()->getId() ){

            $em->remove($publication);
            $flush = $em->flush();

            if ($flush == null){
                $status = 'La publicacion se ha borrado correctamente';
            } else {
                $status = 'Ha ocurrido un error y no se pudo eliminar la publicacion';
            }

        } else {
            $status = 'No puedes borrar la publicacion porque no te pertenece';
        }

        return new Response($status);

    }
}
