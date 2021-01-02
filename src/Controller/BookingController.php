<?php

namespace App\Controller;

use App\Entity\Ad;
use App\Entity\Booking;
use App\Entity\Comment;
use App\Form\BookingType;
use App\Form\CommentType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class BookingController extends AbstractController
{
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }
    /**
     * permet de faire une réservation
     * @Route("/ads/{slug}/booking", name="booking_create")
     * @IsGranted("ROLE_USER")
     */
    public function book(Ad $ad, Request $request)
    {
        $booking = new Booking();
        $form = $this->createForm(BookingType::class, $booking, [
            "validation_groups" => ['default', 'front']
        ]);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $booking->setBooker($this->getUser())
                    ->setAd($ad)
                    ;

            //Si les dates ne sont pas disponible

            $this->em->persist($booking);
            if(!$booking->isBookableDates()){
                $this->addFlash('warning','Les dates que vous avez choisie sont déja prise');
            }else{
                $this->em->flush();

                return $this->redirectToRoute('booking_show', ['id' => $booking->getId(), 'success'=> true]);
            }
            
            
        }

        return $this->render('booking/book.html.twig', [
            'ad' => $ad,
            'form' => $form->createView()
        ]);
    }
    /**
     * permet d'afficher la page d'une réservation
     * @Route("/booking/{id}", name="booking_show")
     * @param Booking $booking
     * @return Response
     */
    public function show(Booking $booking, Request $request){
        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            $comment->setAd($booking->getAd())
                    ->setAuthor($this->getUser())
            ;

            $this->em->persist($comment);
            $this->em->flush();

            $this->addFlash('success','Votre commentaire à bien été pris en compte');
        }

        return $this->render('booking/show.html.twig', [
            'booking' => $booking,
            'form' => $form->createView()
        ]);
    }
}
