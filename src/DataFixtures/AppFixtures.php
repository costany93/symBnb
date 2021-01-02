<?php

namespace App\DataFixtures;

use App\Entity\Ad;
use App\Entity\Booking;
use App\Entity\Comment;
use App\Entity\Image;
use App\Entity\Role;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }
    public function load(ObjectManager $manager)
    {
        $faker = Factory::create('fr_FR');

        //ici on crée un role administrateur
        $adminrole = new Role();
        $adminrole->setTitle('ROLE_ADMIN');
        $manager->persist($adminrole);

        $adminUser = new User();

        //On crée un nouvelle utilisateur qui a le role d'administrateur
        $adminUser->setFirstname('kandza')
                    ->setLastname('prince')
                    ->setEmail('kandzaprince@gmail.com')
                    ->setHash($this->encoder->encodePassword($adminUser, 'password'))
                    ->setPicture('https://randomuser.me/api/portraits/men/41.jpg')
                    ->setIntro($faker->sentence())
                    ->setDescription($faker->paragraph(4))
                    ->addUserRole($adminrole)
        ;
        $manager->persist($adminUser);


        //on initilialse un tableau d'utilisateur pour lié des utilisateurs aux annonces
        
        $users = [];
        $genres = ['male', 'female'];

        //nous gérons les utilisateurs
        for($i = 0; $i<10; $i++){
            $user = new User();
            $genre = $faker->randomElement($genres);

            //gestion de la photo de l'avatar avec l APi randomuser.me
            $picture = 'https://randomuser.me/api/portraits/';
            $picture_id = $faker->numberBetween(0,99).'.jpg';

            if($genre == "male"){
                $picture = $picture .'men/'.$picture_id;
            }else{
                $picture = $picture .'female/'.$picture_id;
            }

            $hash = $this->encoder->encodePassword($user, 'password');
            $user->setFirstname($faker->firstName($genre))
                ->setLastname($faker->lastName)
                ->setEmail($faker->email)
                ->setIntro($faker->sentence())
                ->setDescription($faker->paragraph(4))
                ->setHash($hash)
                ->setPicture($picture)
                ;
            $manager->persist($user);
            $users[] = $user;
        }

        //nous gérons les annonces
        for($i = 0; $i<5; $i++){
            $ad = new Ad();
            $title =$faker->sentences(2,true);
            //on prend un utilisateur au hasard dans notre tableau pour le lié a une annonce
            $user = $users[mt_rand(0,count($users) - 1)];
            $ad->setTitle($title)
                ->setCoverImage($faker->imageUrl(1000,350))
                ->setIntroduction($faker->paragraph(2))
                ->setPrice($faker->numberBetween(2,80))
                ->setRooms($faker->numberBetween(2, 10))
                ->setContenu($faker->sentences(25, true))
                ->setAuthor($user)
                ;
                //Gestion des images
                for($j = 0;$j < mt_rand(2,5); $j++){
                    $image = new Image();

                    $image->setUrl($faker->imageUrl(1000,350))
                          ->setCaption($faker->sentence())
                          ->setAd($ad);
                    $manager->persist($image);
                }
                //Gestion des reservations
                for($j = 0;$j < mt_rand(0, 10); $j++){
                    $booking = new Booking();
                    
                    //Gestion de la date 
                    $createdAt = $faker->dateTimeBetween('-6 months');
                    $startDate = $faker->dateTimeBetween('-3 months');

                    $duration = mt_rand(3,10);

                    //Gestion de la date de fin
                    $endDate = (clone $startDate)->modify("".$duration." days");

                    $amount = $ad->getPrice() * $duration;

                    $booker = $users[mt_rand(0,count($users) - 1)];

                    $comment = $faker->paragraph();

                    $booking->setStartDate($startDate)
                            ->setEndDate($endDate)
                            ->setBooker($booker)
                            ->setAd($ad)
                            ->setAmount($amount)
                            ->setCreatedAt($createdAt)
                            ->setComment($comment)
                    ;

                    $manager->persist($booking);

                    // gestion des commentaire
                    if(mt_rand(0, 1)){
                        $commentBooking = new Comment();

                        $commentBooking->setContent($faker->paragraph())
                                ->setRating(mt_rand(0,5))
                                ->setAuthor($booker)
                                ->setAd($ad)
                                ;
                        $manager->persist($commentBooking);
                    }



                }
            // $product = new Product();
            $manager->persist($ad);
        }

        $manager->flush();
    }
}
