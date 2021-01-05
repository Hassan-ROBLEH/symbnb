<?php

namespace App\Controller;

use App\Service\Stats;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminDashboardController extends AbstractController
{
    /**
     * @Route("/admin", name="admin_dashboard")
     */
    public function index(EntityManagerInterface $manager, Stats $statsService): Response
    {

        // 1ere methode
        //$bookings = $manager->createQuery('SELECT count(b) FROM App\Entity\Booking b')->getSingleScalarResult();
        //$comments = $manager->createQuery('SELECT count(c) FROM App\Entity\Comment c')->getSingleScalarResult();

        // 2eme méthode
        /* $users = $stats->getUsersCount();
        $ads = $stats->getAdsCount();
        $bookings = $stats->getBookingCount();
        $comment = $stats->getcommentCount(); */

        // 3eme methode
        $stats = $statsService->getStats();

        // 1er méthode
        /* $bestAds = $manager->createQuery(
            'SELECT AVG(c.rating) as note, a.title, a.id, u.firstName, u.lastName, u.picture
            FROM App\Entity\Comment c
            JOIN c.ad a
            JOIN a.author u
            GROUP BY a
            ORDER BY note DESC'
        )
            ->setMaxResults(5)
            ->getResult()
        ;
        
        $worstAds = $manager->createQuery(
            'SELECT AVG(c.rating) as note, a.title, a.id, u.firstName, u.lastName, u.picture
            FROM App\Entity\Comment c
            JOIN c.ad a
            JOIN a.author u
            GROUP BY a
            ORDER BY note ASC'
        )
            ->setMaxResults(5)
            ->getResult()
        ;
        */

        // 2eme méthode
        $bestAds = $statsService->getAdsStats('DESC');
        $worstAds = $statsService->getAdsStats('ASC');

        return $this->render('admin/dashboard/index.html.twig', [
            'stats' => /* 1er methode[
                'users' => $users,
                'ads' => $ads,
                'bookings' => $bookings,
                'comments' => $comments
            ] */
            /* éeme methode : compact('users', 'ads', 'bookings', 'comments'), */
            $stats,
            'bestAds' => $bestAds,
            'worstAds' => $worstAds
        ]);
    }
}
