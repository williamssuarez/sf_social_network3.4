<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * User
 *
 * @ORM\Table(name="users", uniqueConstraints={@ORM\UniqueConstraint(name="users_uniques_fields", columns={"email", "nick"})})
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 */
class User implements UserInterface
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string|null
     *
     * @ORM\Column(name="role", type="string", length=20, nullable=true, options={"default"="NULL"})
     */
    private $role = 'NULL';

    /**
     * @var string|null
     *
     * @ORM\Column(name="email", type="string", length=255, nullable=true, options={"default"="NULL"})
     */
    private $email = 'NULL';

    /**
     * @var string|null
     *
     * @ORM\Column(name="name", type="string", length=255, nullable=true, options={"default"="NULL"})
     */
    private $name = 'NULL';

    /**
     * @var string|null
     *
     * @ORM\Column(name="surname", type="string", length=255, nullable=true, options={"default"="NULL"})
     */
    private $surname = 'NULL';

    /**
     * @var string|null
     *
     * @ORM\Column(name="password", type="string", length=255, nullable=true, options={"default"="NULL"})
     */
    private $password = 'NULL';

    /**
     * @var string|null
     *
     * @ORM\Column(name="nick", type="string", length=50, nullable=true, options={"default"="NULL"})
     */
    private $nick = 'NULL';

    /**
     * @var string|null
     *
     * @ORM\Column(name="bio", type="string", length=255, nullable=true, options={"default"="NULL"})
     */
    private $bio = 'NULL';

    /**
     * @var string|null
     *
     * @ORM\Column(name="active", type="string", length=2, nullable=true, options={"default"="NULL"})
     */
    private $active = 'NULL';

    /**
     * @var string|null
     *
     * @ORM\Column(name="image", type="string", length=255, nullable=true, options={"default"="NULL"})
     */
    private $image = 'NULL';

    //CREANDO METODOS PERSONALIZADOS
    public function getUsername(){
        return $this->email;
    }

    public function getSalt(){
        return null;
    }

    public function getRoles(){
        return array('ROLE_USER', 'ROLE_ADMIN');
    }

    public function eraseCredentials(){

    }

    public function __toString(){

        return $this->name;
    }

    //METODOS DEFINIDOS POR LA ENTIDAD AUTOMATICAMENTE
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRole(): ?string
    {
        return $this->role;
    }

    public function setRole(?string $role): self
    {
        $this->role = $role;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getSurname(): ?string
    {
        return $this->surname;
    }

    public function setSurname(?string $surname): self
    {
        $this->surname = $surname;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(?string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getNick(): ?string
    {
        return $this->nick;
    }

    public function setNick(?string $nick): self
    {
        $this->nick = $nick;

        return $this;
    }

    public function getBio(): ?string
    {
        return $this->bio;
    }

    public function setBio(?string $bio): self
    {
        $this->bio = $bio;

        return $this;
    }

    public function getActive(): ?string
    {
        return $this->active;
    }

    public function setActive(?string $active): self
    {
        $this->active = $active;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): self
    {
        $this->image = $image;

        return $this;
    }


}
