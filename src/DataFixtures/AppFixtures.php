<?php

namespace App\DataFixtures;

use App\Entity\MicroPost;
use App\Entity\User;
use DateTime;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    public function __construct(private UserPasswordHasherInterface $userPasswordHasher)
    {
        
    }
    public function load(ObjectManager $manager): void
    {
        $user1 = new User();
        $user1->setEmail('test1@email.com');
        $user1->setPassword($this->userPasswordHasher->hashPassword(
            $user1,
            'password'
        ));
        $user1->setRoles(['ROLE_COMMENTER']);
        $manager->persist($user1);

        $user2 = new User();
        $user2->setEmail('test2@email.com');
        $user2->setPassword($this->userPasswordHasher->hashPassword(
            $user2,
            'password'
        ));
        $user2->setRoles(['ROLE_EDITOR']);
        $manager->persist($user2);

      

        $microPost1 = new MicroPost();
        $microPost1->setTitle('Welcome to our post');
        $microPost1->setText('Welcome to Uk');
        $microPost1->setCreated(new DateTime());
        $microPost1->setAuthor($user1);
        $manager->persist($microPost1);

        $microPost2 = new MicroPost();
        $microPost2->setTitle('Welcome to our post 2');
        $microPost2->setText('Welcome to Uk and england');
        $microPost2->setCreated(new DateTime());
        $microPost2->setAuthor($user2);
        $manager->persist($microPost2);
        
        $microPost3 = new MicroPost();
        $microPost3->setTitle('Welcome to our post 3');
        $microPost3->setText('Welcome to Nepal ');
        $microPost3->setCreated(new DateTime());
        $microPost3->setAuthor($user1);
        $manager->persist($microPost3);
        
        $manager->flush();
    }
}
