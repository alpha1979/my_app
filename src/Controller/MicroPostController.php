<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\MicroPost;
use App\Entity\User;
use App\Form\CommentType;
use App\Form\MicroPostFormType;
use App\Repository\CommentRepository;
use App\Repository\MicroPostRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MicroPostController extends AbstractController
{
    #[Route('/micro-post', name: 'app_micro_post')]
    public function index(MicroPostRepository $microPostRepository): Response
    {
        // $posts = $microPostRepository->findAll();
        $posts = $microPostRepository->findByAllWithComments();
        return $this->render('micro_post/index.html.twig', [
            'posts' => $posts,
        ]);
    }

    #[Route('/micro-post/top-liked', name: 'app_micro_post_top_liked')]
    public function topLiked(MicroPostRepository $microPostRepository): Response
    {
        // $posts = $microPostRepository->findAll();
        $posts = $microPostRepository->findAllWithMinLikes(1);
        return $this->render('micro_post/top_liked.html.twig', [
            'posts' => $posts,
        ]);
    }

    #[Route('/micro-post/follows', name: 'app_micro_post_follows')]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function follows(MicroPostRepository $microPostRepository): Response
    {
        /** @var User $currentUser */
        $currentUser = $this->getUser();
        // $posts = $microPostRepository->findAll();
        $posts = $microPostRepository->findAllByAuthors($currentUser->getFollows());
        return $this->render('micro_post/follows.html.twig', [
            'posts' => $posts,
        ]);
    }

    #[Route('/micro-post/{post}', name: 'app_micro_post_show')]
    #[IsGranted(MicroPost::VIEW, 'post')]
    public function show_one(MicroPost $post): Response
    {
        return $this->render('micro_post/show_one.html.twig', [
            'post' => $post,
        ]);
    }

    #[Route('/micro-post/add', name: 'app_micro_post_add')]
    #[IsGranted('ROLE_WRITER')]
    public function add(Request $request, MicroPostRepository $microPostRepository): Response
    {
        // dd($this->getUser());
        // $this->denyAccessUnlessGranted(
        //     'IS_AUTHENTICATED_FULLY'
        // );
        $form = $this->createForm(MicroPostFormType::class, new MicroPost());
        
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $post = $form->getData();
            $post->setAuthor($this->getUser());
            $microPostRepository->save($post, true);
            $this->addFlash('success', ' Your post was saved');
            return $this->redirectToRoute('app_micro_post');
        }

        return $this->render('micro_post/add.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/micro-post/{post}/edit', name: 'app_micro_post_edit')]
    #[IsGranted(MicroPost::EDIT, 'post')]
    public function edit(MicroPost $post, Request $request, MicroPostRepository $microPostRepository): Response
    {
        $form = $this->createForm(MicroPostFormType::class, $post);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $post = $form->getData();
            $microPostRepository->save($post, true);
            $this->addFlash('success', ' Your post was Updated');
            return $this->redirectToRoute('app_micro_post');
        }

        return $this->render('micro_post/edit.html.twig', [
            'form' => $form,
            'post' => $post
        ]);
    }

    #[Route('/micro-post/{post}/comment', name: 'app_micro_post_add_comment')]
    #[IsGranted(MicroPost::VIEW, 'post')]
    public function addcomment(MicroPost $post, Request $request, CommentRepository $comments): Response
    {
        $form = $this->createForm(CommentType::class, new Comment());
        
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $comment = $form->getData();
            $comment->setPost($post);
            $comment->setAuthor($this->getUser());
            $comments->save($comment, true);
            $this->addFlash('success', ' Your comment was saved');
            return $this->redirectToRoute('app_micro_post_show',[
                'post' => $post->getId()
            ]);
        }

        return $this->render('micro_post/add_comment.html.twig', [
            'form' => $form,
            'post' => $post
        ]);
    }
}
