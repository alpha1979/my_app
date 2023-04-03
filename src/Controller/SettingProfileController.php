<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\User;
use App\Entity\UserProfile;
use App\Form\ProfileImageType;
use App\Form\UserProfileFormType;
use App\Repository\UserRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class SettingProfileController extends AbstractController
{
    #[Route('/setting/profile', name: 'app_setting_profile')]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function profile(Request $request, UserRepository $userRepository): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        // dd($user->getUserProfile());
        $userProfile = $user->getUserProfile() ?? new UserProfile();
        // dd($userProfile);
        $form = $this->createForm(UserProfileFormType::class, $userProfile);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $userProfile = $form->getData();
            $user->setUserProfile($userProfile);
            $userRepository->save($user, true);
            $this->addFlash('success', 'Your user profile is now updated');
            return $this->redirectToRoute('app_micro_post');
        }


        return $this->render('setting_profile/profile.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/setting/profile-image', name: 'app_setting_profile_image')]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function profileImage(Request $request, SluggerInterface $slugger, UserRepository $users): Response
    {
        $form = $this->createForm(ProfileImageType::class);

        /** @var User $user */
        $user = $this->getUser();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $profileImageFIle = $form->get('profileImage')->getData();

            if ($profileImageFIle) {
                $originalFileName = pathinfo(
                    $profileImageFIle->getClientOriginalName(),
                    PATHINFO_FILENAME
                );

                $safeFileName = $slugger->slug($originalFileName);
                $newFileName = $safeFileName . '-' . uniqid() . '.' . $profileImageFIle->guessExtension();

                try {
                    $profileImageFIle->move(
                        $this->getParameter('profiles_directory'),
                        $newFileName
                    );
                } catch(FileException $ex) {

                }
                // dd($user->getUserProfile());
                $profile = $user->getUserProfile() ?? new UserProfile();
                // dd($profile);
                $profile->setImage($newFileName);
                $user->setUserProfile($profile);
                $users->save($user, true);
                $this->addFlash('success', 'Your image was uploaded successfully');
                return $this->redirectToRoute('app_setting_profile_image');
            }
        }
        return $this->render(
            'setting_profile/profile_image.html.twig',
            [
                'form' => $form->createView()
            ]
        );
    }
}
