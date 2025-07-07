<?php

/**
 * @copyright 2025 Konrad Stomski
 *
 * @license MIT
 */

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Category;
use App\Form\CategoryType;
use App\Repository\CategoryRepository;
use App\Service\Interface\CategoryServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Controller for handling category-related actions.
 */
#[Route('/{_locale}/categories', requirements: ['_locale' => 'en|pl'], defaults: ['_locale' => 'en'])]
class CategoryController extends AbstractController
{
    /**
     * Constructor.
     *
     * @param CategoryServiceInterface $categoryService    Category service
     * @param TranslatorInterface      $translator         Translator
     * @param SluggerInterface         $slugger            Slugger
     * @param CategoryRepository       $categoryRepository Category repository
     */
    public function __construct(private readonly CategoryServiceInterface $categoryService, private readonly TranslatorInterface $translator, private readonly SluggerInterface $slugger, private readonly CategoryRepository $categoryRepository)
    {
    }

    /**
     * Index action.
     *
     * @return Response HTTP response
     */
    #[Route(
        name: 'app_category_index',
        methods: 'GET'
    )]
    public function index(): Response
    {
        $categories = $this->categoryRepository->findAllWithArticleCount();

        return $this->render('category/index.html.twig', ['categories' => $categories]);
    }

    /**
     * Create action.
     *
     * @param Request $request HTTP request
     *
     * @return Response HTTP response
     */
    #[Route(
        '/new',
        name: 'app_category_new',
        methods: 'GET|POST'
    )]
    #[IsGranted('ROLE_ADMIN')]
    public function new(Request $request): Response
    {
        $category = new Category();
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $name = $category->getName();
            if ($name) {
                $category->setSlug($this->slugger->slug($name)->lower()->toString());
            } else {
                $category->setSlug('');
            }

            $this->categoryService->create($category);

            $this->addFlash(
                'success',
                $this->translator->trans('message.created_successfully')
            );

            return $this->redirectToRoute('app_category_index');
        }

        return $this->render('category/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * View action.
     *
     * @param string $slug Category slug
     *
     * @return Response HTTP response
     */
    #[Route(
        '/{slug}',
        name: 'app_category_show',
        methods: 'GET'
    )]
    public function show(string $slug): Response
    {
        $category = $this->categoryRepository->findBySlug($slug);

        if (!$category) {
            throw $this->createNotFoundException('Category not found');
        }

        // Populate the virtual articleCount property
        $category->articleCount = $this->categoryRepository->getArticleCount($category);

        return $this->render(
            'category/show.html.twig',
            ['category' => $category]
        );
    }

    /**
     * Edit action.
     *
     * @param Request $request HTTP request
     * @param string  $slug    Category slug
     *
     * @return Response HTTP response
     */
    #[Route(
        '/{slug}/edit',
        name: 'app_category_edit',
        methods: 'GET|PUT'
    )]
    #[IsGranted('ROLE_ADMIN')]
    public function edit(Request $request, string $slug): Response
    {
        $category = $this->categoryRepository->findBySlug($slug);

        if (!$category) {
            throw $this->createNotFoundException('Category not found');
        }

        $form = $this->createForm(
            CategoryType::class,
            $category,
            [
                'method' => 'PUT',
                'action' => $this->generateUrl('app_category_edit', ['slug' => $category->getSlug()]),
            ]
        );
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $name = $category->getName();
            if ($name) {
                $category->setSlug($this->slugger->slug($name)->lower()->toString());
            }
            $this->categoryService->update($category);

            $this->addFlash(
                'success',
                $this->translator->trans('message.edited_successfully')
            );

            return $this->redirectToRoute('app_category_index');
        }

        return $this->render(
            'category/edit.html.twig',
            [
                'form' => $form->createView(),
                'category' => $category,
            ]
        );
    }

    /**
     * Delete action.
     *
     * @param Request $request HTTP request
     * @param string  $slug    Category slug
     *
     * @return Response HTTP response
     */
    #[Route(
        '/{slug}/delete',
        name: 'app_category_delete',
        methods: 'GET|POST|DELETE'
    )]
    #[IsGranted('ROLE_ADMIN')]
    public function delete(Request $request, string $slug): Response
    {
        $category = $this->categoryRepository->findBySlug($slug);

        if (!$category) {
            throw $this->createNotFoundException('Category not found');
        }

        // Check if category has articles
        $articleCount = $this->categoryRepository->getArticleCount($category);

        if ($request->isMethod('POST') || $request->isMethod('DELETE')) {
            $this->categoryService->delete($category);

            $this->addFlash(
                'success',
                $this->translator->trans('message.deleted_successfully')
            );

            return $this->redirectToRoute('app_category_index');
        }

        return $this->render(
            'category/delete.html.twig',
            [
                'category' => $category,
                'articleCount' => $articleCount,
            ]
        );
    }
}
