<?php

namespace App\Controller;

use App\Entity\Booking;
use App\Form\AdmimBookingType;
use App\Repository\BookingRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class AdminBookingController extends AbstractController
{
    public function __construct(EntityManagerInterface $em, BookingRepository $br)
    {
        $this->em = $em;
        $this->br = $br;
    }
    /**
     * @Route("/admin/bookings", name="admin_bookings_index")
     */
    public function index()
    {
        $bookings = $this->br->findAll();
        return $this->render('admin/booking/index.html.twig', [
            'bookings' => $bookings,
        ]);
    }

    /**
     * permet de modifier une réservation
     * @Route("admin/bookings/{id}/edit", name="admin_booking_edit")
     * @param Booking $booking
     */
    public function edit(Booking $booking, Request $request){
        $form = $this->createForm(AdmimBookingType::class, $booking);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $booking->setAmount(0);
            $this->em->persist($booking);
            $this->em->flush();
            $this->addFlash("success", "Votre réservations à été modifié");

            return $this->redirectToRoute("admin_bookings_index");
        }
        return $this->render("admin/booking/edit.html.twig", [
            'booking' => $booking,
            'form' => $form->createView()
        ]);
    }

    /**
     * permet de supprimer une annonce
     * @Route("/admin/bookings/{id}/delete", name="admin_booking_delete")
     * @param Booking $booking
     */
    public function delete(Booking $booking){
        $this->em->remove($booking);
        $this->em->flush();

        $this->addFlash("success","Annonce suprimé avec success");

        return $this->redirectToRoute("admin_bookings_index");
    }
}
