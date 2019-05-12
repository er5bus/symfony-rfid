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

        $reservationsRepository = $em->getRepository(Reservation::class);
        $bookRepository = $em->getRepository(Book::class);
        $userRepository = $em->getRepository(User::class);

        $totalBooks = $bookRepository->countAllBooks();
        $totalReservations = $reservationsRepository->countAllReservations();
        $totalUsers = $userRepository->countAllUsers();

        $reservationsStats = [];
        foreach ($reservationsRepository->reservationStats() as $value){

            if (!$value['createdAt'] instanceof \DateTime){
                continue;
            }

            $key = $value['createdAt']->format('m-Y');
            if (isset($reservationsStats[$key])){
                $reservationsStats[$key]['value'] = $reservationsStats[$key]['value'] + $value['borrowedQuantity'];
                continue;
            }

            $reservationsStats[$key] = [
                'date' => $key,
                'value' => $value['borrowedQuantity']
            ];
        }

        return $this->render('dashboard/index.html.twig', [
            'total_books' => $totalBooks['total'] ?? 0,
            'total_reservations' => $totalReservations['total'] ?? 0,
            'total_users' => $totalUsers['total'] ?? 0,
            'reservations_stats_date' => array_column(array_values($reservationsStats), 'date'),
            'reservations_stats_value' => array_column(array_values($reservationsStats), 'value'),
        ]);
    }
}