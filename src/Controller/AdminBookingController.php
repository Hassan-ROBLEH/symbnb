<?php

namespace App\Controller;

use App\Entity\Booking;
use App\Form\AdminBookingType;
use App\Repository\BookingRepository;
use App\Service\Pagination;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminBookingController extends AbstractController
{
    /**
     * @Route("/admin/bookings/{page<\d+>?1}", name="admin_booking_index")
     */
    public function index(BookingRepository $repo, $page, Pagination $pagination): Response
    {

        $pagination->setEntityClass(Booking::class)
                   ->setPage($page)
                   /* ->setRoute('admin_booking_index') */
        ;

        //$bookings = $pagination->getData();
        //dump($bookings);
        //die;

        //$limit = 10;

        //$start = $page * $limit - $limit;

        //$total = count($repo->findAll());

        //$pages = ceil($total / $limit);

        return $this->render('admin/booking/index.html.twig', [
            /* 'bookings' => $pagination->getData(),
            'pages' => $pagination->getPages(),
            'page' => $page */
            'pagination' => $pagination
        ]);
    }

    /**
     * Permet d'éditer une réservation
     * 
     * @Route("/admin/bookings/{id}/edit", name="admin_booking_edit")
     *
     * @return Response
     */
    public function edit(Booking $booking, Request $request, EntityManagerInterface $manager) {
        $form = $this->createForm(AdminBookingType::class, $booking);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {

            //$booking->setAmount($booking->getAd()->getPrice() * $booking->getDuration());
            $booking->setAmount(0);

            $manager->persist($booking);
            $manager->flush();

            $this->addFlash(
                'success',
                "La réservation n°{$booking->getId()} a bien été modifiée !"
            );
        }

        return $this->render('admin/booking/edit.html.twig', [
            'form' => $form->createView(),
            'booking' => $booking
        ]);
    }

    /**
     * Permet de supprimer une réservation
     * 
     * @Route("/admin/bookings/{id}/delete", name="admin_booking_delete")
     *
     * @return void
     */
    public function delete(Booking $booking, EntityManagerInterface $manager) {

        $manager->remove($booking);
        $manager->flush();

        $this->addFlash(
            'success',
            "La réservation a bien été supprimée"
        );
 
        return $this->redirectToRoute("admin_booking_index");
    }
}
