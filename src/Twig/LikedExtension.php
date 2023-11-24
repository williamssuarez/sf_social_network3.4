<?php


namespace App\Twig;


use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Doctrine\ORM\EntityManagerInterface;

class LikedExtension extends AbstractExtension
{
    protected $doctrine;

    //DEFINIENDO CON LO QUE VA A TRABAJAR LA EXTENSION
    public function __construct(EntityManagerInterface $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    //CREANDO EL FILTRO, COMO SE LLAMARA A LA FUNCION DESDE TWIG Y DONDE ESTA EL METODO A EJECUTAR
    public function getFilters()
    {
        return array(
            //SE LLAMARA EN LA PLANTILLA AL FILTRO POR LIKED
            //EL METODO ES $THIS PORQUE ESTA AQUI MISMO Y SE LLAMA LIKEDFILTER
            new TwigFilter('liked', [$this, 'likedFilter'])
        );
    }

    //EL METODO QUE USA EL FILTRO
    public function likedFilter($user, $publication){

        //OBTENER EL REPOSITORIO
        $like_repo = $this->doctrine->getRepository(\App\Entity\Like::class);
        //ENCONTRAR SI LA PUBLICACION COINCIDE CON EL USUARIO QUE ESTA LOGEADO
        $publication_liked = $like_repo->findOneBy(array(
            "user" => $user,
            "publication" => $publication
        ));

        //SI EN LA TABLA DE LIKES EN LA DB HAY UN REGISTRO CON LA ID DEL USUARIO ACTUAL Y LA PUBLICATION QUE LE PASAMOS A LA FUNCION, SIGNIFICA QUE LE GUSTO
        if(!empty($publication_liked) && is_object($publication_liked)){
            $result = true; //HAY UN LIKE EN LA TABLA, ES DECIR QUE LE GUSTA ESA PUBLICACION AL USUARIO
        } else {
            $result = false;//CASO CONTRARIO, NO LE GUSTA
        }

        return $result; //DEVOLVER RESULTADO
    }

    //COMO SE VA A LLAMAR A LA EXTENSION DESDE EL YAML DE SERVICES
    public function getName(){

        return 'liked_extension';
    }

}