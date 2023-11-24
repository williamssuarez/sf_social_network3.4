<?php


namespace App\Services;

//ENTIDADES
use App\Entity\Notification;

class NotificationService
{
    public $manager;

    public function __construct($manager)
    {
        $this->manager = $manager;
    }

    public function set($user, $type, $typeId, $extra){
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

}