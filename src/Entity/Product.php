<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Product
 *
 * @ORM\Table(name="product" ,  indexes={@ORM\Index(name="idx_name", columns={"name"})})
 * @ORM\Entity(repositoryClass="App\Repository\ProductRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Product
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
     *
     * @ORM\Column(name="name", type="string", length=100, nullable=false)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="Description", type="text", length=65535, nullable=false)
     */
    private $description;

    /**
     * @var int
     *
     * @ORM\Column(name="price", type="integer", nullable=false)
     */
    private $price;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="create_at", type="datetime", nullable=false)
     */
    private $createAt;

    /**
     * @var string|null
     *
     * @ORM\Column(name="picture", type="string", length=100, nullable=true)
     */
    private $picture;

    /**
     * @var \user
     *
     * @ORM\ManyToOne(targetEntity="User" , inversedBy="products")
     */
    private $user;

    /**
     * @var \favorite
     *
     * @ORM\OneToMany(targetEntity="Favorite" , mappedBy="product")
     */
    private $favorite;



    public function getid(): ?int
    {
        return $this->id;
    }

    public function setid(int $id): self
    {
        $this->id= $id;

        return $this;
    }


    public function getname(): ?string
    {
        return $this->name;
    }

    public function setname(string $name): self
    {
        $this->name= $name;

        return $this;
    }

    public function getprice(): ?int
    {
        return $this->price;
    }

    public function setprice(int $price): self
    {
        $this->price= $price;

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



    public function getCreateAt(): ?\DateTimeInterface
    {
        return $this->createAt;
    }

    /**
     * @param \DateTimeInterface $createAt
     */
    public function setCreateAt(\DateTimeInterface $createAt): self
    {
        $this->createAt = $createAt;

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

    public function getpictureUploadDir()
    {
        return 'upload/Product';
    }

    public function getpictureWebPath()
    {
        if($this->getpicture()) {
            return $this->getpictureUploadDir() . '/' . $this->getpicture();
        }
    }

    public function getpictureUploadRootDir()
    {
        return realpath(__DIR__.'/../../public').'/'.$this->getpictureUploadDir();
    }

    public function getpictureAbsolutePath()
    {
        return null === $this->getpicture()
            ? null
            : $this->getpictureUploadRootDir().'/'.$this->getpicture();
    }

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

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(User $user): self
    {
        $this->user= $user;

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
    public function __toString()
    {
        return $this->name;
    }


    /**
     * @ORM\PrePersist()
     */
    public function beforeAdd()
    {
        $dateTime = new \DateTime();
        $this->setCreateAt($dateTime);
    }
}
