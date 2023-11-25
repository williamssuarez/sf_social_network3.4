<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

//SERVICIOS
use Knp\Component\Pager\PaginatorInterface;
use App\Services\NotificationService; //SERVICIO DE NOTIFICACION PERSONALIZADO

class NotificationController extends AbstractController
{
    private $paginator;
    private $notification;

    public function __construct(PaginatorInterface $paginator,
                                NotificationService $notification)
    {
        $this->paginator = $paginator;
        $this->notification = $notification;
    }

    //METODO BASICO PARA LISTAR EN PANTALLA TODAS LAS NOTIFICACIONES DEL USUARIO
    public function index(Request $request): Response
    {
        $em = $this->getDoctrine()->getManager();

        $user = $this->getUser();
        $user_id = $user->getId();

        $dql = "SELECT n FROM App\Entity\Notification n WHERE n.user = $user_id ORDER BY n.id DESC";
        $query = $em->createQuery($dql);

        $notifications = $this->paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            5
        );

        $this->notification->read($user);

        return $this->render('notification/notification_page.html.twig', array(
            'user' => $user,
            'pagination' => $notifications
        ));
    }

    //OBTENER EL TOTAL DE NOTIFICACIONES PENDIENTES POR AJAX
    public function countNotifications(): Response
    {
        $em = $this->getDoctrine()->getManager();
        $notification_repo = $em->getRepository(\App\Entity\Notification::class);
        $notifications = $notification_repo->findBy(array(
           'user' => $this->getUser(),
            'readed' => 0
        ));

        return new Response(count($notifications));
    }
}
