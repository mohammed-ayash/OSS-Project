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
        //check the file if UploadedFile
        if ($uploadedFile instanceof  UploadedFile)
        {
            //uuid4 use for create file name string
            $Uuid4 = Uuid::uuid4();
            $filename="{$Uuid4->toString()}.jpg";
            $storeFolder = $user->getpictureUploadDir();
            //delete picture from the source
            $user->deleteCurrentpicture();
            $uploadedFile->move($storeFolder,$filename);
            $user->setPicture($filename);
            $em->flush();
        }
    }
}
