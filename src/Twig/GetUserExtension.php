<?php


namespace App\Twig;


use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Doctrine\ORM\EntityManagerInterface;

class GetUserExtension extends AbstractExtension
{
    private $doctrine;

    public function __construct(EntityManagerInterface $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    public function getFilters()
    {
        return array(
            new TwigFilter('get_user', [$this, 'get_user_filter'])
        );
    }

    public function get_user_filter($user_id)
    {
        $user_repo = $this->doctrine->getRepository(\App\Entity\User::class);
        $user = $user_repo->findOneBy(array(
            "id" => $user_id
        ));

        if(!empty($user) && is_object($user)){
            $result = $user;
        } else {
            $result = false;
        }

        return $result;
    }

    public function getName(){
        return 'liked_extension';
    }

}