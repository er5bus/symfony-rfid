<?php


namespace App\Controller;

use App\Entity\Book;
use App\Entity\Reservation;
use App\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/dashboard")
 * @Security("is_granted('ROLE_SUPER_ADMIN') or is_granted('ROLE_LIBRARIAN')")
 */
class DashboardController extends Controller
{
    /**
     * @Route("/", name="dashboard_index", methods={"GET"})
     */
    public function index(): Response
    {
        $em = $this->getDoctrine()->getManager();

        $totalBooks = $em->getRepository(Book::class)->countAllBooks();
        $totalReservations = $em->getRepository(Reservation::class)->countAllReservations();
        $totalUsers = $em->getRepository(User::class)->countAllUsers();
        return $this->render('dashboard/index.html.twig', [
            'total_books' => $totalBooks['total'] ?? 0,
            'total_reservations' => $totalReservations['total'] ?? 0,
            'total_users' => $totalUsers['total'] ?? 0
        ]);
    }
}