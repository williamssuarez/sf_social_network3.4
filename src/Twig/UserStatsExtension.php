<?php


namespace App\Twig;


use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Doctrine\ORM\EntityManagerInterface;

class UserStatsExtension extends AbstractExtension
{
    protected $doctrine;

    public function __construct(EntityManagerInterface $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    public function getFilters()
    {
        return array(
            new TwigFilter('user_stats', [$this, 'userStatsFilter'])
        );
    }

    public function userStatsFilter($user){

        //OBTENER LOS REPOSITORIOS
        $following_repo = $this->doctrine->getRepository(\App\Entity\Following::class);
        $like_repo = $this->doctrine->getRepository(\App\Entity\Like::class);
        $publication_repo = $this->doctrine->getRepository(\App\Entity\Publication::class);

        //HACER LAS CONSULTAS
        $user_following = $following_repo->findBy(array('user' => $user));
        $user_followers = $following_repo->findBy(array('followed' => $user));
        $user_publications = $publication_repo->findBy(array('user' => $user));
        $user_likes = $like_repo->findBy(array('user' => $user));

        //CONTARLAS Y METERLAS EN EL ARRAY
        $result = array(
          'following' => count($user_following),
          'followers' => count($user_followers),
          'publications' => count($user_publications),
          'likes' => count($user_likes)
        );

        return $result;

    }

    public function getName(){
        return 'user_stats_extension';
    }

}