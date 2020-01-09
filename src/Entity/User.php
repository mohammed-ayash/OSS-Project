<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * User
 *
 * @ORM\Table(name="user")
 * @UniqueEntity(fields="email", message="This email is already taken")
 * @UniqueEntity(fields="username",  message="This username is already taken")
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 */
class User implements UserInterface , \Serializable
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
     * @var string
     * @ORM\Column(name="username", type="string", length=100, nullable=false)
     * @Assert\NotBlank(message="Username is required")
     * @Assert\Length(max=100,maxMessage="Maximum length is 100")
     */
    private $username;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=100, nullable=false)
     * @Assert\NotBlank(message="Email is required")
     * @Assert\Email(message="Please Enter a valid email")
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(name="password", type="string", length=100, nullable=false)
     * @Assert\NotBlank(message="password is required")
     */
    private $password;

    /**
     * @var string|null
     *
     * @ORM\Column(name="picture", type="string", length=100, nullable=true)
     */
    private $picture;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="Birthday", type="datetime", nullable=false)
     */
    private $birthday;

    /**
     * @var bool
     *
     * @ORM\Column(name="type", type="boolean", nullable=false)
     */
    private $type;

    /**
     * @var bool
     *
     * @ORM\Column(name="isAdmin", type="boolean", nullable=false)
     */
    private $isAdmin;


    /**
     * @var \Country
     *
     * @ORM\ManyToOne(targetEntity="Country")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="country_id", referencedColumnName="id")
     * })
     */
    private $country;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Product", mappedBy="User")
     */
    private $products;

    /**
     * @var \favorite
     *
     * @ORM\OneToMany(targetEntity="Favorite" , mappedBy="product")
     */
    private $favorite;

    //construct
    public function __construct()
    {
        $this->products = new ArrayCollection();
    }

    //get array product
    /**
     * @return Collection|Product[]
     */
    public function getProducts(): Product
    {
        return $this->products;
    }

    public function getid(): ?int
    {
        return $this->id;
    }

    public function setid(int $id): self
    {
        $this->id= $id;

        return $this;
    }


    public function getusername(): ?string
    {
        return $this->username;
    }

    public function setusername(?string $username): self
    {
        $this->username= $username;

        return $this;
    }

    public function email(): ?string
    {
        return $this->email;
    }

    public function setemail(?string $email): self
    {
        $this->email= $email;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setpassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getPicture(): ?string
    {
        return $this->picture;
    }

    public function setPicture(?string $picture): self
    {
        $this->picture = $picture;

        return $this;
    }

    //get picture Directory path
    public function getpictureUploadDir()
    {
        return '/upload/User';
    }

    //get full picture path
    public function getpictureWebPath()
    {
        if($this->getpicture()) {
            return $this->getpictureUploadDir() . '/' . $this->getpicture();
        }
    }
    //get picture path from the root
    public function getpictureUploadRootDir()
    {
        return realpath(__DIR__.'/../../public').'/'.$this->getpictureUploadDir();
    }

    //get full picture path from the root
    public function getpictureAbsolutePath()
    {
        return null === $this->getpicture()
            ? null
            : $this->getpictureUploadRootDir().'/'.$this->getpicture();
    }

    //delete picture from database then from the path .
    public function deleteCurrentpicture()
    {
        $fullPath = $this->getpictureAbsolutePath();
        $this->setpicture(null);
        if($fullPath) {
            if (file_exists($fullPath)) {
                unlink($fullPath);
            }
        }
    }

    /**
     * @ORM\PostRemove()
     */
    public function removepictureFile()
    {
        $this->deleteCurrentpicture();
    }



    public function getbirthday(): ?\DateTimeInterface
    {
        return $this->birthday;
    }

    public function setbirthday(?\DateTimeInterface $birthday): self
    {
        $this->birthday = $birthday;

        return $this;
    }

    public function gettype(): ?bool
    {
        return $this->type;
    }

    public function settype (bool $type): self
    {
        $this->type= $type;

        return $this;
    }

    public function getCountry(): ?Country
    {
        return $this->country;
    }

    public function setCountry(?Country $country): self
    {
        $this->country = $country;

        return $this;
    }

    public function getFavorite(): ?Favorite
    {
        return $this->favorite;
    }

    public function setFavorite(Favorite $favorite): self
    {
        $this->favorite= $favorite;

        return $this;
    }


    public function getdescription(): ?string
    {
        return $this->description;
    }

    public function setdescription(string $description): self
    {
        $this->description= $description;

        return $this;
    }

    //set time now
    /**
     * @ORM\PrePersist()
     */
    public function beforeAdd()
    {
        $dateTime = new \DateTime();
        $this->setCreateAt($dateTime);
    }

    public function __toString()
    {
        return $this->username;
    }

    //authondcition
    public function getRoles()
    {
        if ($this->isAdmin)
            return['ROLE_Admin'];
        if($this->type)
            return ['ROLE_owner'];
        else
            return ['ROLE_customer'];

    }

    public function getSalt()
    {
        return null;
    }

    public function eraseCredentials()
    {

    }

    public function serialize()
    {
        return serialize([
            $this->id,
            $this->username,
            $this->password
        ]);
    }

    public function unserialize($serialized)
    {
        list(
            $this->id,
            $this->username,
            $this->password) = unserialize($serialized);
    }

}
