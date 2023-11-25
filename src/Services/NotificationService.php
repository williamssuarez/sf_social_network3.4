<?php


namespace App\Services;

//ENTIDADES
use App\Entity\Notification;

//SERVICIOS
use Doctrine\ORM\EntityManagerInterface;

class NotificationService
{
    private $manager;

    public function __construct(EntityManagerInterface $manager)
    {
        $this->manager = $manager;
    }

    public function set($user, $type, $typeId, $extra = null){
        $em = $this->manager;

        $notification = new Notification();
        $notification->setUser($user);
        $notification->setType($type);
        $notification->setTypeId($typeId);
        $notification->setReaded(0);
        $notification->setCreatedAt(new \DateTime("now"));
        $notification->setExtra($extra);

        $em->persist($notification);
        $flush = $em->flush();

        if($flush == null){
            $status = "Persistido exitosamente";
        } else {
            $status = "Ha ocurrido un error";
        }

        return $status;
    }

    public function read($user){

        $em = $this->manager;

        $notifications_repo = $em->getRepository( \App\Entity\Notification::class);
        $notifications = $notifications_repo->findBy(array('user' => $user ));

        foreach ($notifications as $notification){
            $notification->setReaded(1);
            $em->persist($notification);
        }

        $flush = $em->flush();

        if ($flush == null){
            return true;
        } else {
            return false;
        }

        return true;
    }

}