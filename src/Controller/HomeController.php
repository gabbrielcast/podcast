<?php

namespace App\Controller;

use App\Repository\PodcastRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(PodcastRepository $podcastRepository): Response
    {
        return $this->render('home/index.html.twig', [
            'podcasts' => $podcastRepository->findFirsts(),
        ]);
    }
}
