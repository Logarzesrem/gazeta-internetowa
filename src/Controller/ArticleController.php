<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Category;
use App\Entity\Comment;
use App\Form\ArticleType;
use App\Form\CommentType;
use App\Repository\ArticleRepository;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\String\Slugger\SluggerInterface;

/**
 * Controller for managing articles.
 */
class ArticleController extends AbstractController
{
    /**
     * ArticleController constructor.
     */
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly ArticleRepository $articleRepository,
        private readonly CategoryRepository $categoryRepository,
        private readonly SluggerInterface $slugger,
    ) {
    }

    /**
     * Display a paginated and sortable list of articles.
     */
    #[\Symfony\Component\Routing\Attribute\Route('/{_locale}/articles', name: 'app_article_index', methods: ['GET'], requirements: ['_locale' => 'en|pl'], defaults: ['_locale' => 'en'])]
    public function index(Request $request): Response
    {
        $page = $request->query->getInt('page', 1);
        $sortField = $request->query->get('sort', 'createdAt');
        $sortDirection = $request->query->get('direction', 'DESC');
        $result = $this->articleRepository->findPaginated($page, 10, $sortField, $sortDirection);
        $categories = $this->categoryRepository->findAllWithArticleCount();

        return $this->render('article/index.html.twig', [
            'articles' => $result['items'],
            'total' => $result['total'],
            'page' => $page,
            'limit' => 10,
            'categories' => $categories,
            'sortField' => $sortField,
            'sortDirection' => $sortDirection,
        ]);
    }

    /**
     * Display articles for a given category.
     */
    #[\Symfony\Component\Routing\Attribute\Route('/{_locale}/articles/category/{slug}', name: 'app_article_by_category', methods: ['GET'], requirements: ['_locale' => 'en|pl'], defaults: ['_locale' => 'en'])]
    public function byCategory(Category $category, Request $request): Response
    {
        $page = $request->query->getInt('page', 1);
        $result = $this->articleRepository->findByCategoryPaginated($category, $page);
        $categories = $this->categoryRepository->findAllWithArticleCount();

        return $this->render('article/by_category.html.twig', [
            'category' => $category,
            'articles' => $result['items'],
            'total' => $result['total'],
            'page' => $page,
            'limit' => 10,
            'categories' => $categories,
        ]);
    }

    /**
     * Show a single article and its comments.
     */
    #[\Symfony\Component\Routing\Attribute\Route('/{_locale}/articles/{id}', name: 'app_article_show', methods: ['GET'], requirements: ['_locale' => 'en|pl', 'id' => '\\d+'], defaults: ['_locale' => 'en'])]
    public function show(int $id): Response
    {
        $article = $this->articleRepository->find($id);
        if (!$article) {
            throw $this->createNotFoundException('Article not found');
        }
        // Log the ID for debugging
        error_log('Article ID: ' . $id);
        $categories = $this->categoryRepository->findAllWithArticleCount();

        $comment = new Comment();
        $comment->setArticle($article);
        $commentForm = $this->createForm(CommentType::class, $comment);

        return $this->render('article/show.html.twig', [
            'article' => $article,
            'categories' => $categories,
            'comment_form' => $commentForm->createView(),
        ]);
    }

    /**
     * Create a new article.
     */
    #[\Symfony\Component\Routing\Attribute\Route('/{_locale}/articles/new', name: 'app_article_new', methods: ['GET', 'POST'], requirements: ['_locale' => 'en|pl'], defaults: ['_locale' => 'en'])]
    #[IsGranted('ROLE_ADMIN')]
    public function new(Request $request): Response
    {
        $article = new Article();
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $article->setAuthor($this->getUser());
            $this->entityManager->persist($article);
            $this->entityManager->flush();

            $this->addFlash('success', 'Article created successfully.');

            return $this->redirectToRoute('app_article_show', ['id' => $article->getId()]);
        }

        return $this->render('article/new.html.twig', [
            'article' => $article,
            'form' => $form,
        ]);
    }

    /**
     * Edit an existing article.
     */
    #[\Symfony\Component\Routing\Attribute\Route('/{_locale}/articles/{id}/edit', name: 'app_article_edit', methods: ['GET', 'POST'], requirements: ['_locale' => 'en|pl'], defaults: ['_locale' => 'en'])]
    #[IsGranted('ROLE_ADMIN')]
    public function edit(Request $request, Article $article): Response
    {
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $article->setUpdatedAt(new \DateTimeImmutable());
            $this->entityManager->flush();

            $this->addFlash('success', 'Article updated successfully.');

            return $this->redirectToRoute('app_article_show', ['id' => $article->getId()]);
        }

        return $this->render('article/edit.html.twig', [
            'article' => $article,
            'form' => $form,
        ]);
    }

    /**
     * Show a confirmation form before deleting an article.
     */
    #[\Symfony\Component\Routing\Attribute\Route('/{_locale}/articles/{id}/delete', name: 'app_article_delete_confirm', methods: ['GET'], requirements: ['_locale' => 'en|pl'], defaults: ['_locale' => 'en'])]
    #[IsGranted('ROLE_ADMIN')]
    public function deleteConfirm(int $id): Response
    {
        $article = $this->articleRepository->find($id);
        if (!$article) {
            throw $this->createNotFoundException('Article not found');
        }

        return $this->render('article/delete_confirm.html.twig', [
            'article' => $article,
        ]);
    }

    /**
     * Delete an article after confirmation.
     */
    #[\Symfony\Component\Routing\Attribute\Route('/{_locale}/articles/{id}/delete', name: 'app_article_delete', methods: ['POST'], requirements: ['_locale' => 'en|pl'], defaults: ['_locale' => 'en'])]
    #[IsGranted('ROLE_ADMIN')]
    public function delete(Request $request, Article $article): Response
    {
        if ($this->isCsrfTokenValid('delete' . $article->getId(), $request->request->get('_token'))) {
            $this->entityManager->remove($article);
            $this->entityManager->flush();

            $this->addFlash('success', 'Article deleted successfully.');
        }

        return $this->redirectToRoute('app_article_index');
    }
}
