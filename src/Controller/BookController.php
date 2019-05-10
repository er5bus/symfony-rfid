<?php

namespace App\Controller;

use App\Controller\Traits\ControllerTrait;
use App\Entity\Book;
use App\Form\BookType;
use App\Repository\BookRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/book")
 * @Security("is_granted('ROLE_SUPER_ADMIN') or is_granted('ROLE_LIBRARIAN')")
 */
class BookController extends Controller
{
    use ControllerTrait;

    /**
     * @Route("/", name="book_index", methods={"GET"})
     *
     * @param Request $request
     * @param BookRepository $bookRepository
     * @return Response
     */
    public function index(Request $request, BookRepository $bookRepository): Response
    {
        $books = $this->getKnpPaginator()
            ->paginate(
                $bookRepository->getFindAllQuery($request->query->get('search', null)),
                $request->query->getInt('page', 1), /*page number*/
                10/*limit per page*/
            );

        return $this->render('book/index.html.twig', [
            'books' => $books
        ]);
    }

    /**
     * @Route("/new", name="book_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $book = new Book();
        $form = $this->createForm(BookType::class, $book);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($book);
            $entityManager->flush();

            $this->addFlash('success', $this->trans('New book has been created successfully'));
            return $this->redirectToRoute('book_index');
        }

        return $this->render('book/new.html.twig', [
            'book' => $book,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="book_show", methods={"GET"})
     */
    public function show(Book $book): Response
    {
        return $this->render('book/show.html.twig', [
            'book' => $book,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="book_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Book $book): Response
    {
        $form = $this->createForm(BookType::class, $book);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            $this->addFlash('success', $this->trans('The book has been updated successfully'));
            return $this->redirectToRoute('book_index', [
                'id' => $book->getId(),
            ]);
        }

        return $this->render('book/edit.html.twig', [
            'book' => $book,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="book_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Book $book): Response
    {
        if ($this->isCsrfTokenValid('delete'.$book->getId(), $request->request->get('_token'))) {
            try{
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->remove($book);
                $entityManager->flush();

                $this->addFlash('success', $this->trans('The book has been deleted successfully'));
            }catch (\Exception $exception){
                $this->addFlash('danger', $this->trans('The book can not be deleted'));
            }
        }

        return $this->redirectToRoute('book_index');
    }
}
