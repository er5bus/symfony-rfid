<?php

namespace App\Controller;

use App\Controller\Traits\ControllerTrait;
use App\Entity\Category;
use App\Form\CategoryType;
use App\Repository\CategoryRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/category")
 * @Security("is_granted('ROLE_SUPER_ADMIN') or is_granted('ROLE_LIBRARIAN')")
 */
class CategoryController extends Controller
{
    use ControllerTrait;

    /**
     * @Route("/", name="category_index", methods={"GET"})
     * @param Request $request
     * @param CategoryRepository $categoryRepository
     * @return Response
     */
    public function index(Request $request, CategoryRepository $categoryRepository): Response
    {
        $categories = $this->getKnpPaginator()
            ->paginate(
                $categoryRepository->getFindAllQuery($request->query->get('search', null)),
                $request->query->getInt('page', 1), /*page number*/
                10 /*limit per page*/
            );

        return $this->render('category/index.html.twig', [
            'categories' => $categories
        ]);
    }

    /**
     * @Route("/new", name="category_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $category = new Category();
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($category);
            $entityManager->flush();

            $this->addFlash('success', $this->trans('New category has been created'));
            return $this->redirectToRoute('category_index');
        }

        return $this->render('category/new.html.twig', [
            'category' => $category,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="category_show", methods={"GET"})
     */
    public function show(Category $category): Response
    {
        return $this->render('category/show.html.twig', [
            'category' => $category,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="category_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Category $category): Response
    {
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            $this->addFlash('success', $this->trans('The category has been created successfully'));
            return $this->redirectToRoute('category_index', [
                'id' => $category->getId(),
            ]);
        }

        return $this->render('category/edit.html.twig', [
            'category' => $category,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="category_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Category $category): Response
    {
        if ($this->isCsrfTokenValid('delete'.$category->getId(), $request->request->get('_token'))) {
            try{
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->remove($category);
                $entityManager->flush();

                $this->addFlash('success', $this->trans('The category has been deleted successfully'));
            }catch (\Exception $exception){
                $this->addFlash('danger', $this->trans('The category can not be deleted'));
            }
        }

        return $this->redirectToRoute('category_index');
    }
}
