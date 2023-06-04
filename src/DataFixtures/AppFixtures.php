<?php

namespace App\DataFixtures;

use App\Entity\Podcast;
use App\Entity\User;
use DateTime;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $user=new User();
        $user->setEmail('admin@doctoforum.com');
        $user->setNombre('Admin');
        $user->setApellidos('Admin Admin');
        $user->setRoles(['ROLE_ADMIN']);
        $user->setPassword(password_hash('doctoforum',PASSWORD_DEFAULT));

        $manager->persist($user);   
        $manager->flush();


        $user2=new User();
        $user2->setEmail('bromero@ser.com');
        $user2->setNombre('Berto');
        $user2->setApellidos('Romero');
        
        $user2->setPassword(password_hash('123456',PASSWORD_DEFAULT));

        $manager->persist($user2);   
        $manager->flush();

        $user3=new User();
        $user3->setEmail('abretos@ser.com');
        $user3->setNombre('Aimar');
        $user3->setApellidos('Bretos');
        
        $user3->setPassword(password_hash('123456',PASSWORD_DEFAULT));

        $manager->persist($user3);   
        $manager->flush();



        $podcast=new Podcast();
        $podcast->setTitulo('Nadie Sabe Nada');
        $podcast->setDescripcion('Nadie Sabe Nada podcast en la ser');
        $podcast->setImagen('nadie-sabe-nada.png');
        $podcast->setAudio('nadie-sabe-nada-audio.mp3');
        $podcast->setAutor($user2);
        $podcast->setFechaSubida(new DateTime());

        $manager->persist($podcast);   
        $manager->flush();



        $podcast2=new Podcast();
        $podcast2->setTitulo('Hora 25');
        $podcast2->setDescripcion('Hora 25 podcast en la ser');
        $podcast2->setImagen('hora-25.jpg');
        $podcast2->setAudio('hora-25-audio.mp3');
        $podcast2->setAutor($user3);
        $podcast2->setFechaSubida(new DateTime());

        $manager->persist($podcast2);   
        $manager->flush();

    }
}
