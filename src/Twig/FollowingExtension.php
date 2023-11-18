<?php


namespace App\Twig;


use Twig\Extension\AbstractExtension; //ESTO REEMPLAZA AL \TWIG_EXTENSION
use Twig\TwigFilter; //ESTO REEMPLAZA AL TWIG_SIMPLEFILTER
use Doctrine\ORM\EntityManagerInterface; //ESTO REEMPLAZA AL REGISTRYINTERFACE

class FollowingExtension extends AbstractExtension
{
    protected $doctrine;

    public function __construct(EntityManagerInterface $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    public function getFilters(){

        return array(
          new TwigFilter('following', [$this, 'followingFilter'])
        );

    }

    public function followingFilter($user, $followed){

        $following_repo = $this->doctrine->getRepository('App\Entity\Following');
        $user_following = $following_repo->findOneBy(array(
           "user" => $user,
           "followed" => $followed
        ));

        if (!empty($user_following) && is_object($user_following)){
            $result = true;
        } else {
            $result = false;
        }

        return $result;

    }

    public function getName(){

        return 'following_extension';

    }

}