<?php

namespace App\Controller;

use App\Entity\Podcast;
use App\Entity\User;
use App\Repository\PodcastRepository;
use App\Repository\UserRepository;
use App\Service\FileUploader;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class AdminController extends AbstractController
{
    #[Route('/admin/user', name: 'users')]
    public function index(UserRepository $userRepository): Response
    {
        
        $usuarios=new ArrayCollection($userRepository->findAll());
        $usuarios_f=$usuarios->filter(function($user){
            return $user->getId()!=$this->getUser()->getId();
        });
        return $this->render('user/index.html.twig', [
           'usuarios' => $usuarios_f,
           'user'=>$this->getUser()
        ]);
    }

    #[Route('/profile/nuevo-podcast', name: 'app_podcast', methods:['GET','POST'])]
    public function nuevo(Request $request, SluggerInterface $slugger,
        PodcastRepository $repository, UserRepository $userRepository): Response
    {
        $podcast = new Podcast();

        $form='';
        $user_is_admin=in_array('ROLE_ADMIN',$this->getUser()->getRoles());

        if($user_is_admin){
            $form=$this->createFormBuilder($podcast)
            ->add('titulo', TextType::class)
            ->add('descripcion', TextType::class)
            ->add('autorId', NumberType::class,['data_class'=>null,'empty_data'=>'','mapped' => false])
            ->add('imagen', FileType::class)
            ->add('audio', FileType::class)
            ->add('save', SubmitType::class, ['label' => 'Create Podcast'])
            ->getForm();
        }else{
            $form = $this->createFormBuilder($podcast)
            ->add('titulo', TextType::class)
            ->add('descripcion', TextType::class)
            ->add('imagen', FileType::class)
            ->add('audio', FileType::class)
            ->add('save', SubmitType::class, ['label' => 'Create Podcast'])
            ->getForm();
    
        }

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
                if($user_is_admin){
                    $user_target=$userRepository->findBy(['id'=>$form->get('autorId')->getData()])[0];

                    $podcast->setAutor($user_target);
                }else{
                    $podcast->setAutor($this->getUser());
                }
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


    #[Route('/admin/user-nuevo', name: 'user_nuevo', methods:['GET','POST'])]
    public function nuevoUser(Request $request, UserRepository $repository): Response
    {
        $user = new User();

        $form=$this->createFormBuilder($user)
        ->add('nombre', TextType::class)
        ->add('apellidos', TextType::class)
        ->add('password', TextType::class)
        ->add('email', TextType::class)
        ->add('roles', TextType::class,['data_class'=>null,'empty_data'=>'','mapped' => false, 
            'label'=>'Roles: 1 admin 0 normal'
        ])
        ->add('save', SubmitType::class, ['label' => 'Create User'])
        ->getForm();
    
     

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $user = $form->getData();
            $user->setPassword(password_hash($form->get('password')->getData(),PASSWORD_DEFAULT));
            
            if($form->get('roles')->getData() === 1 ){
                $user->setRoles(['ROLE_ADMIN']);
            }
            
            $repository->save($user,true);
            return $this->redirectToRoute('users');
        }


        return $this->render('user/nuevo.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/admin/user/edit/{id}', name: 'edit_user', methods:['GET','POST'])]
    public function editUser(User $user,Request $request, UserRepository $repository): Response
    {

        $form=$this->createFormBuilder($user)
        ->add('nombre', TextType::class)
        ->add('apellidos', TextType::class)
        ->add('password', TextType::class,['required'=>false,'data_class'=>null,'empty_data'=>'','mapped' => false])
        ->add('email', TextType::class)
        ->add('roles', TextType::class,['data_class'=>null,'empty_data'=>'','mapped' => false, 
            'label'=>'Roles: 1 admin 0 normal'
        ])
        ->add('save', SubmitType::class, ['label' => 'Save User'])
        ->getForm();
    
     

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $user = $form->getData();
            if($form->get('password')->getData()!==''){

                $user->setPassword(password_hash($form->get('password')->getData(),PASSWORD_DEFAULT));
            }
            
            if($form->get('roles')->getData() === 1 ){
                $user->setRoles(['ROLE_ADMIN']);
            }
            
            $repository->save($user,true);
            return $this->redirectToRoute('users');
        }


        return $this->render('user/nuevo.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/profile/delete/{id}',name:'delete_podcast',methods:['GET','POST'])]
    public function delete(Podcast $podcast, Request $request,PodcastRepository $repository)
    {
        $repository->remove($podcast,true);

        return $this->redirectToRoute('_profiler');
    }


    
    #[Route('/admin/delete/{id}',name:'delete_user',methods:['GET','POST'])]
    public function deleteUser(User $user,UserRepository $repository)
    {
        $repository->remove($user,true);

        return $this->redirectToRoute('users');
    }
    

}
