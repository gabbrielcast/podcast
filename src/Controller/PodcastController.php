<?php

namespace App\Controller;

use App\Entity\Podcast;
use App\Repository\PodcastRepository;
use App\Service\FileUploader;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Validator\Constraints\File;

class PodcastController extends AbstractController
{


    #[Route('/profile/nuevo-podcast', name: 'app_podcast', methods:['GET','POST'])]
    public function nuevo(Request $request, SluggerInterface $slugger, PodcastRepository $repository): Response
    {
        $podcast = new Podcast();

        $form = $this->createFormBuilder($podcast)
        ->add('titulo', TextType::class)
        ->add('descripcion', TextType::class)
        ->add('imagen', FileType::class)
        ->add('audio', FileType::class)
        ->add('save', SubmitType::class, ['label' => 'Create Podcast'])
        ->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $imagenFile = $form->get('imagen')->getData();
            $audioFile = $form->get('audio')->getData();


            $destination = $this->getParameter('uploads');

            
    
            try {
                $fileUploader= new FileUploader($destination,$slugger);
                $imagenName=$fileUploader->upload($imagenFile);
                $audioName=$fileUploader->upload($audioFile);
                
                $podcast->setImagen($imagenName);
                $podcast->setAudio($audioName);
                $podcast->setAutor($this->getUser());
                $podcast->setFechaSubida(new DateTime());
              
            } catch (FileException $e) {
                return $this->render('podcast/index.html.twig', [
                    'form' => $form,
                ]);
            }

            $repository->save($podcast,true);
            
            return $this->redirectToRoute('_profiler');
        }


        return $this->render('podcast/index.html.twig', [
            'form' => $form,
        ]);
    }


    #[Route('/profile/edit/{id}', name: 'edit_podcast', methods:['GET','POST'])]
    public function edit(Podcast $podcast,Request $request, SluggerInterface $slugger, PodcastRepository $repository): Response
    {
        
        $imagenOriginalName=$podcast->getImagen();
        $audioOriginalName=$podcast->getAudio();

        $form = $this->createFormBuilder($podcast)
        ->add('titulo', TextType::class)
        ->add('descripcion', TextType::class)
        ->add('imagen', FileType::class,['required'=>false,'data_class'=>null,'empty_data'=>''])
        ->add('audio', FileType::class,['required'=>false,'data_class'=>null,'empty_data'=>''])
        ->add('save', SubmitType::class, ['label' => 'Edit Podcast'])
        ->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $imagenFile = $form->get('imagen')->getData();
            $audioFile = $form->get('audio')->getData();

            $destination = $this->getParameter('uploads');

            
    
            try {
                $fileUploader= new FileUploader($destination,$slugger);

                if($imagenFile && $fileUploader->delete($imagenOriginalName)){
                    $imagenName=$fileUploader->upload($imagenFile);
                    $podcast->setImagen($imagenName);
                }

                if($audioFile && $fileUploader->delete($audioOriginalName)){
                    $audioName=$fileUploader->upload($audioFile);
                    $podcast->setAudio($audioName);
                }
              
            } catch (FileException $e) {
                return $this->render('podcast/index.html.twig', [
                    'form' => $form,
                ]);
            }

            $repository->save($podcast,true);
            
            return $this->redirectToRoute('_profiler');
        }


        return $this->render('podcast/index.html.twig', [
            'form' => $form,
        ]);
    }


}