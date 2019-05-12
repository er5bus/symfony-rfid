<?php

namespace App\Controller;

use App\Controller\Traits\ControllerTrait;
use App\Entity\Book;
use App\Entity\Reservation;
use App\Form\MakeReservationType;
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
                9 /*limit per page*/
        );

        // parameters to template
        return $this->render('book_shop/book_index.html.twig', array('books' => $books));
    }

    /**
     * @Route("/book/details/{id}", name="book_shop_show", methods={"GET"})
     * @param Book $book
     * @return Response
     */
    public function show(Book $book): Response
    {
        $reservedBooks = $this->getDoctrine()
            ->getManager()
            ->getRepository(Reservation::class)
            ->findByBook($book->getId());

        $inStock = $reservedBooks['quantity'] - $reservedBooks['borrowedQuantity'];

        return $this->render('book_shop/book_show.html.twig', [
            'book' => $book,
            'in_stock' => $inStock
        ]);
    }

    public function renderMakeReservationType(book $book)
    {
        $reservation = (new Reservation())
            ->setBook($book)
            ->setUser($this->getUser())
        ;

        $form = $this->createForm(MakeReservationType::class, $reservation, [
            'action' => $this->generateUrl('user_make_reservation')
        ]);

        return $this->render('book_shop/_reservation_form.html.twig', [
            'book' => $book,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/make/reservation", name="user_make_reservation", methods={"POST"})
     * @param Request $request
     * @return Response
     * @throws Exception
     */
    public function makeReservation(Request $request): Response
    {
        $reservation = new Reservation();
        $form = $this->createForm(MakeReservationType::class, $reservation, [
            'action' => $this->generateUrl('user_make_reservation')
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){

            $this->getDoctrine()->getManager()->persist($reservation);
            $this->getDoctrine()->getManager()->flush();

            return $this->json([
                'success' => true,
                'msg' => $this->trans('Your request has been verified and awaiting approval')
            ]);
        }

        return $this->json([
            'success' => false,
            'msg' => $this->trans('Your request is not verified. Refresh the page and try again.')
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
            $reservationRepository->findByUserQuery($user->getId(), $request->query->get('search', null)),
            $request->query->getInt('page', 1), /*page number*/
            10 /*limit per page*/
        );

        // parameters to template
        return $this->render('book_shop/reservations.html.twig', [
            'reservations' => $reservations
        ]);
    }
}