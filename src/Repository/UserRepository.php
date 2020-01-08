<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Ramsey\Uuid\Uuid;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
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

    public function uploadPicture(User $user, $uploadedFile)
    {
        $em = $this->getEntityManager();
        if ($uploadedFile instanceof  UploadedFile)
        {
            $Uuid4 = Uuid::uuid4();
            $filename="{$Uuid4->toString()}.jpg";
            $storeFolder = $user->getpictureUploadDir();
            $user->deleteCurrentpicture();
            $uploadedFile->move($storeFolder,$filename);
            $user->setPicture($filename);
            $em->flush();
        }
    }
}
