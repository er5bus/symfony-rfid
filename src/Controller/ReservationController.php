<?php

namespace App\Controller;

use App\Controller\Traits\ControllerTrait;
use App\Entity\Reservation;
use App\Form\ReservationType;
use App\Repository\ReservationRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/reservation")
 * @Security("is_granted('ROLE_SUPER_ADMIN') or is_granted('ROLE_LIBRARIAN')")
 */
class ReservationController extends Controller
{
    use ControllerTrait;

    /**
     * @Route("/", name="reservation_index", methods={"GET"})
     * @param Request $request
     * @param ReservationRepository $reservationRepository
     * @return Response
     */
    public function index(Request $request, ReservationRepository $reservationRepository): Response
    {
        $reservations = $this->getKnpPaginator()
            ->paginate(
                $reservationRepository->getFindAllQuery($request->query->get('search', null)),
                $request->query->getInt('page', 1), /*page number*/
                10 /*limit per page*/
            );

        return $this->render('reservation/index.html.twig', [
            'reservations' => $reservations
        ]);
    }

    /**
     * @Route("/new", name="reservation_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $reservation = new Reservation();
        $form = $this->createForm(ReservationType::class, $reservation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($reservation);
            $entityManager->flush();
            $this->addFlash('success', $this->trans('New reservation has been created successfully'));

            return $this->redirectToRoute('reservation_index');
        }

        return $this->render('reservation/new.html.twig', [
            'reservation' => $reservation,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="reservation_show", methods={"GET"})
     */
    public function show(Reservation $reservation): Response
    {
        return $this->render('reservation/show.html.twig', [
            'reservation' => $reservation,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="reservation_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Reservation $reservation): Response
    {
        $form = $this->createForm(ReservationType::class, $reservation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('success', $this->trans('The reservation has been updated successfully'));
            return $this->redirectToRoute('reservation_index', [
                'id' => $reservation->getId(),
            ]);
        }

        return $this->render('reservation/edit.html.twig', [
            'reservation' => $reservation,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="reservation_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Reservation $reservation): Response
    {
        if ($this->isCsrfTokenValid('delete'.$reservation->getId(), $request->request->get('_token'))) {
            try{
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->remove($reservation);
                $entityManager->flush();

                $this->addFlash('success', $this->trans('The reservation has been deleted successfully'));
            }catch (\Exception $exception){
                $this->addFlash('danger', $this->trans('The reservation can not be deleted'));
            }
        }

        return $this->redirectToRoute('reservation_index');
    }
}
