<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Category;
use App\Form\CategoryType;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\String\Slugger\SluggerInterface;

class CategoryController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly CategoryRepository $categoryRepository,
        private readonly SluggerInterface $slugger,
    ) {
    }

    #[\Symfony\Component\Routing\Attribute\Route('/{_locale}/categories', name: 'app_category_index', methods: ['GET'], requirements: ['_locale' => 'en|pl'], defaults: ['_locale' => 'en'])]
    public function index(): Response
    {
        $categories = $this->categoryRepository->findAllWithArticleCount();

        return $this->render('category/index.html.twig', [
            'categories' => $categories,
        ]);
    }

    #[\Symfony\Component\Routing\Attribute\Route('/{_locale}/categories/new', name: 'app_category_new', methods: ['GET', 'POST'], requirements: ['_locale' => 'en|pl'], defaults: ['_locale' => 'en'])]
    #[IsGranted('ROLE_ADMIN')]
    public function new(Request $request): Response
    {
        $category = new Category();
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $name = $category->getName() ?? '';
            if ('' !== $name) {
                $category->setSlug($this->slugger->slug($name)->lower()->toString());
            } else {
                $category->setSlug('');
            }

            if ($form->isValid()) {
                $this->entityManager->persist($category);
                $this->entityManager->flush();

                return $this->redirectToRoute('app_category_show', ['slug' => $category->getSlug()]);
            }
        }

        return $this->render('category/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[\Symfony\Component\Routing\Attribute\Route('/{_locale}/categories/{slug}', name: 'app_category_show', methods: ['GET'], requirements: ['_locale' => 'en|pl'], defaults: ['_locale' => 'en'])]
    public function show(Category $category): Response
    {
        // Fetch all articles for this category (no pagination for now)
        $articles = $this->entityManager->getRepository(\App\Entity\Article::class)->findByCategoryPaginated($category, 1, 1000)['items'];

        return $this->render('category/show.html.twig', [
            'category' => $category,
            'articles' => $articles,
        ]);
    }

    #[\Symfony\Component\Routing\Attribute\Route('/{_locale}/categories/{slug}/edit', name: 'app_category_edit', methods: ['GET', 'POST'], requirements: ['_locale' => 'en|pl'], defaults: ['_locale' => 'en'])]
    #[IsGranted('ROLE_ADMIN')]
    public function edit(Request $request, Category $category): Response
    {
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $category->setSlug($this->slugger->slug($category->getName())->lower()->toString());
            $this->entityManager->flush();

            $this->addFlash('success', 'Category updated successfully.');

            return $this->redirectToRoute('app_category_show', ['slug' => $category->getSlug()]);
        }

        return $this->render('category/edit.html.twig', [
            'category' => $category,
            'form' => $form,
        ]);
    }

    #[\Symfony\Component\Routing\Attribute\Route('/{_locale}/categories/{slug}', name: 'app_category_delete', methods: ['POST'], requirements: ['_locale' => 'en|pl'], defaults: ['_locale' => 'en'])]
    #[IsGranted('ROLE_ADMIN')]
    public function delete(Request $request, Category $category): Response
    {
        if ($this->isCsrfTokenValid('delete' . $category->getId(), $request->request->get('_token'))) {
            $this->entityManager->remove($category);
            $this->entityManager->flush();

            $this->addFlash('success', 'Category deleted successfully.');
        }

        return $this->redirectToRoute('app_category_index');
    }
}
