<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

//SERVICIOS
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Knp\Component\Pager\PaginatorInterface;

//ENTIDADES
use App\Entity\User;
use App\Entity\PrivateMessage;

//FORMS
use App\Form\PrivateMessageType;

class PrivateMessageController extends AbstractController
{
    private $session;
    private $paginator;

    public function __construct(SessionInterface $session,
                                PaginatorInterface $paginator)
    {
        $this->session = $session;
        $this->paginator = $paginator;
    }

    public function index(Request $request): Response
    {
        $em = $this->getDoctrine()->getManager();
        $message_repo = $em->getRepository(\App\Entity\PrivateMessage::class);
        $user = $this->getUser();

        $private_message = new PrivateMessage();
        $form = $this->createForm(PrivateMessageType::class, $private_message, array(
            'empty_data' => $user
        ));

        $form->handleRequest($request);

        if($form->isSubmitted()){
            if($form->isValid()){

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
                        $file->move("uploads/messages/images", $file_name);

                        //GUARDANDO EL NOMBRE DE LA IMAGEN EN LA BASE DE DATOS
                        $private_message->setImage($file_name);

                    } else {
                        //CASO CONTRARIO, NO ES UNA EXTENSION VALIDA, SE QUEDA EN NULL
                        $private_message->setImage(null);
                    }

                } else {
                    //SI LA IMAGEN ESTA VACIA SE QUEDA EN NULL
                    $private_message->setImage(null);
                }

                //SUBIR DOCUMENTO
                $doc = $form['file']->getData();

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
                        $doc->move("uploads/messages/documents", $file_name);

                        //GUARDANDO EL NOMBRE DEL DOCUMENTO EN LA BASE DE DATOS
                        $private_message->setFile($file_name);

                    } else {
                        //CASO CONTRARIO, NO ES UNA EXTENSION VALIDA, SE QUEDA EN NULL
                        $private_message->setFile(null);
                    }

                } else {
                    //SI EL DOCUMENTO ESTA VACIO O ES NULO SE QUEDA EN NULL
                    $private_message->setFile(null);
                }

                //SUBIENO LA INFO A LA BASE DE DATOS

                //PREPARANDO LOS DATOS RESTANTES
                $private_message->setEmitter($user);
                $private_message->setCreatedAt(new \DateTime("now"));
                $private_message->setReaded(0);

                $em->persist($private_message);
                $flush = $em->flush();

                if ($flush == null){
                    $status = "El mensaje se ha enviado correctamente";
                } else {
                    $status = "Ha ocurrio un error y el mensaje no se enviado, vuelve a intentarlo";
                }

            } else {
                $status = "El mensaje no es valido, intente de nuevo";
            }
            $this->session->getFlashBag()->add("status", $status);
            return $this->redirectToRoute("private_message_index");
        }

        return $this->render('private_message/index.html.twig', array(
            'form' => $form->createView()
        ));
    }

    public function sended(Request $request): Response
    {
        $private_messages = $this->getPrivateMessages($request, "sended");

        return $this->render('private_message/sended.html.twig', array(
            'pagination' => $private_messages
        ));
    }

    public function getPrivateMessages($request, $type = null)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        $user_id = $user->getId();

        if ($type == "sended"){

            $dql = "SELECT p FROM App\Entity\PrivateMessage p WHERE p.emitter = $user_id ORDER BY p.id DESC ";
        } else {

            $dql = "SELECT p FROM App\Entity\PrivateMessage p WHERE p.receiver = $user_id ORDER BY p.id DESC ";
        }

        $query = $em->createQuery($dql);

        $pagination = $this->paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            5);

        return $pagination;
    }
}
