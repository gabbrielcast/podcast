<?php

namespace App\Controller;

use App\Repository\PodcastRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProfileController extends AbstractController
{
    #[Route('/profiler', name: '_profiler')]
    public function index(): Response
    {
        $user=$this->getUser();

        $podcasts=$user->getPodcasts();
        return $this->render('profile/index.html.twig', [
            'user' => $user,
            'podcasts'=>$podcasts->toArray()
        ]);
    }

    #[Route('/profiler/podcasts/{id}', name: 'show_podcast')]
    public function show(int $id,PodcastRepository $podcastRepository): Response
    {
        $user=$this->getUser();
        $podcasts=$podcastRepository->findPodcastByUserExcludeOne($user->getId(),$id);
        $targetPodcast=$podcastRepository->findBy(['id'=>$id])[0];
        return $this->render('profile/show.html.twig', [
            'targetPodcast'=> $targetPodcast,
            'podcasts' => $podcasts,
        ]);
    }
}
