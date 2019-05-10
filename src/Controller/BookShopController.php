<?php

namespace App\Controller;

use App\Controller\Traits\ControllerTrait;
use App\Entity\Reservation;
use App\Repository\BookRepository;
use App\Repository\ReservationRepository;
use DateTime;
use Exception;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * @Security("is_granted('ROLE_USER')")
 */
class BookShopController extends Controller
{
    use ControllerTrait;

    /**
     * @Route("/", name="book_shop", methods={"GET"})
     * @param Request $request
     * @param BookRepository $bookRepository
     * @return Response
     * @throws Exception
     */
    public function index(Request $request, BookRepository $bookRepository): Response
    {
        $books = $this->getKnpPaginator()
            ->paginate(
                $bookRepository->getFindAllQuery($request->query->get('search', null)),
                $request->query->getInt('page', 1), /*page number*/
                10 /*limit per page*/
        );

        // parameters to template
        return $this->render('book_shop/index.html.twig', array('books' => $books));
    }

    /**
     * @Route("/make/reservation", name="user_make_reservation", methods={"POST"})
     * @param Request $request
     * @param BookRepository $bookRepository
     * @return Response
     * @throws Exception
     */
    public function makeReservation(Request $request, BookRepository $bookRepository): Response
    {
        $bookId = $request->request->get('book', null);
        $token = $request->request->get('_token', null);
        $quantity = $request->request->getInt('quantity', null);
        $startBorrowingDate = $request->request->get('start_borrowing_date', null);
        $endBorrowingDate = $request->request->get('end_borrowing_date');

        if (!$this->isCsrfTokenValid('make_reservation'.$bookId, $token)) {
            throw new AccessDeniedException('The CSRF token is invalid.');
        }

        $book = $bookRepository->find($bookId);
        if (
            !is_null($book) &&
            ($quantity > 0 && $quantity < $book->getQuantity()) &&
            $this->validateDate($startBorrowingDate) &&
            $this->validateDate($startBorrowingDate)
        ){
            $reservation = new Reservation();
            $reservation->setBook($book);
            $reservation->setRequestedQuantity($quantity);
            $reservation->setStartBorrowingDate(DateTime::createFromFormat('m/d/Y', $startBorrowingDate));
            $reservation->setEndBorrowingDate(DateTime::createFromFormat('m/d/Y', $endBorrowingDate));
            $reservation->setCreatedAt(new DateTime('now'));
            $reservation->setStatus(Reservation::IN_PENDING);
            $reservation->setUser($this->getUser());

            $this->getDoctrine()->getManager()->persist($reservation);
            $this->getDoctrine()->getManager()->flush();

            return $this->json([
                'success' => true,
                'msg' => $this->trans('Your request has been verified and awaiting approval')
            ]);
        }

        return $this->json([
            'success' => false,
            'msg' => $this->trans('Your request is not verified. Plz refresh the page and try again.')
        ]);
    }

    /**
     * @Route("/reservations", name="user_reservation", methods={"GET"})
     * @param Request $request
     * @param ReservationRepository $reservationRepository
     * @return Response
     * @throws Exception
     */
    public function myReservation(Request $request, ReservationRepository $reservationRepository): Response
    {
        $user = $this->getUser();

        $reservations = $this->getKnpPaginator()->paginate(
            $reservationRepository->getFindByUserQuery($user->getId()),
            $request->query->getInt('page', 1), /*page number*/
            10 /*limit per page*/
        );

        // parameters to template
        return $this->render('book_shop/reservations.html.twig', [
            'reservations' => $reservations
        ]);
    }
}