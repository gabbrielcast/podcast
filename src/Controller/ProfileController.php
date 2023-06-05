<?php

namespace App\Controller;

use App\Repository\PodcastRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProfileController extends AbstractController
{
    #[Route('/profile', name: '_profiler')]
    public function index(PodcastRepository $podcastRepository): Response
    {
        $user=$this->getUser();
        
        if(in_array('ROLE_ADMIN',$user->getRoles())){
            $podcasts=$podcastRepository->findAll();
            return $this->render('profile/index.html.twig', [
                'user' => $user,
                'podcasts'=>$podcasts,
                
            ]);
        }
       
    }

}
