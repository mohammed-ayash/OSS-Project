<?php

namespace App\DataFixtures;
use App\Entity\USer;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }
    public function load(ObjectManager $manager)
    {
        $this->loadUsers($manager);

    }
    public function loadUsers(ObjectManager $manager)
    {
     $user = new User();
     $user->setUsername('storyfile_admin');
     $user->setEmail('storyfile@admin.com');
     //$admin->setPassword('123');
        $user->setPassword($this->passwordEncoder->encodePassword($user,'123456'));
    $manager->persist($user);
    $manager->flush();


    }
}
  
