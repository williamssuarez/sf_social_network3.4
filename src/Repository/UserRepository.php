<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

//ENTIDADES EXTRAS
use App\Entity\Following;

/**
 * @extends ServiceEntityRepository<User>
 *
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(User $entity, bool $flush = true): void
    {
        $this->_em->persist($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function remove(User $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    public function getFollowingUsers($user)
    {
        //OBTENIENDO EL ENTITY MANAGER
        $em = $this->getEntityManager();

        //OBTENIENDO EL REPOSITORIO DE FOLLOWING
        $following_repo = $em->getRepository(\App\Entity\Following::class);
        //OBTENIENDO LOS USUARIOS QUE SIGUES
        $following = $following_repo->findBy(array(
            'user' => $user
        ));

        //CREANDO UN ARRAY DE APOYO
        $following_array = array();

        //GUARDANDO EN EL ARRAY LOS RESULTADOS DE LA QUERY
        foreach ($following as $follow){
            $following_array[] = $follow->getFollowed();
        }

        //OBTENIENDO EL REPOSITORIO DE USUARIOS PARA HACER LA PROXIMA QUERY
        $user_repo = $em->getRepository(\App\Entity\User::class);
        //PREPARANDO LA QUERY
        $user_id = $user->getId();
        $users = $user_repo->createQueryBuilder('u')
                            ->where("u.id != (:user) AND u.id IN (:following)")
                            ->setParameter("user", $user_id)
                            ->setParameter("following", $following_array)
                            ->orderBy('u.id', 'DESC');

        return $users;
    }

    // /**
    //  * @return User[] Returns an array of User objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('u.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?User
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
