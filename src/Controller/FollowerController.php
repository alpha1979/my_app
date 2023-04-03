<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\User;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FollowerController extends AbstractController
{
    #[Route('/follower/{id}', name: 'app_follow')]
    public function follower(User $userToFollow, ManagerRegistry $doctrine, Request $request): Response
    {
        /** @var User $currentUser */
        $currentUser = $this->getUser();
        if ($userToFollow->getId() !== $currentUser->getId()) {
            $currentUser->follow($userToFollow);
            $doctrine->getManager()->flush();
        }
        return $this->redirect($request->headers->get('referer'));
    }

    #[Route('/unfollower/{id}', name: 'app_unfollow')]
    public function unFollower(User $userToUnfollow, ManagerRegistry $doctrine, Request $request): Response
    {
         /** @var User $currentUser */
         $currentUser = $this->getUser();
         if ($userToUnfollow->getId() !== $currentUser->getId()) {
             $currentUser->unFollow($userToUnfollow);
             $doctrine->getManager()->flush();
         }
         return $this->redirect($request->headers->get('referer'));
    }
}
