<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Category;
use App\Entity\Comment;
use App\Form\ArticleType;
use App\Form\CommentType;
use App\Repository\ArticleRepository;
use App\Repository\CategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * ArticleController.
 */
class ArticleController extends AbstractController
{
    /**
     * Constructor.
     *
     * @param ArticleRepository   $articleRepository  Article repository
     * @param CategoryRepository  $categoryRepository Category repository
     * @param TranslatorInterface $translator         Translator for messages
     */
    public function __construct(private readonly ArticleRepository $articleRepository, private readonly CategoryRepository $categoryRepository, private readonly TranslatorInterface $translator)
    {
    }

    /**
     * Display a paginated and sortable list of articles.
     *
     * @param Request $request HTTP request
     *
     * @return Response HTTP response with rendered article list
     */
    #[\Symfony\Component\Routing\Attribute\Route(
        '/{_locale}/articles',
        name: 'app_article_index',
        methods: ['GET'],
        requirements: ['_locale' => 'en|pl'],
        defaults: ['_locale' => 'en']
    )]
    public function index(Request $request): Response
    {
        $page = $request->query->getInt('page', 1);
        $sortField = (string) $request->query->get('sort', 'createdAt');
        $sortDirection = (string) $request->query->get('direction', 'DESC');
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
     *
     * @param Category $category The category to filter by
     * @param Request  $request  HTTP request
     *
     * @return Response HTTP response with rendered category articles
     */
    #[\Symfony\Component\Routing\Attribute\Route(
        '/{_locale}/articles/category/{slug}',
        name: 'app_article_by_category',
        methods: ['GET'],
        requirements: ['_locale' => 'en|pl'],
        defaults: ['_locale' => 'en']
    )]
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
     *
     * @param int $id The article ID
     *
     * @return Response HTTP response with rendered article details
     */
    #[\Symfony\Component\Routing\Attribute\Route(
        '/{_locale}/articles/{id}',
        name: 'app_article_show',
        methods: ['GET'],
        requirements: ['_locale' => 'en|pl', 'id' => '\\d+'],
        defaults: ['_locale' => 'en']
    )]
    public function show(int $id): Response
    {
        $article = $this->articleRepository->findWithCategory($id);
        if (!$article) {
            throw $this->createNotFoundException($this->translator->trans('article.not_found'));
        }

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
     *
     * @param Request $request HTTP request
     *
     * @return Response HTTP response with form or redirect
     */
    #[\Symfony\Component\Routing\Attribute\Route(
        '/{_locale}/articles/new',
        name: 'app_article_new',
        methods: ['GET', 'POST'],
        requirements: ['_locale' => 'en|pl'],
        defaults: ['_locale' => 'en']
    )]
    #[IsGranted('ROLE_ADMIN')]
    public function new(Request $request): Response
    {
        $article = new Article();
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $this->getUser();
            if ($user instanceof \App\Entity\AdminUser) {
                $article->setAuthor($user);
            } else {
                throw new \RuntimeException('Only admin users can create articles');
            }
            $this->entityManager->persist($article);
            $this->entityManager->flush();

            $this->addFlash('success', $this->translator->trans('article.created_successfully'));

            return $this->redirectToRoute('app_article_show', ['id' => $article->getId()]);
        }

        return $this->render('article/new.html.twig', [
            'article' => $article,
            'form' => $form,
        ]);
    }

    /**
     * Edit an existing article.
     *
     * @param Request $request HTTP request
     * @param int     $id      The article ID
     *
     * @return Response HTTP response with form or redirect
     */
    #[\Symfony\Component\Routing\Attribute\Route(
        '/{_locale}/articles/{id}/edit',
        name: 'app_article_edit',
        methods: ['GET', 'POST'],
        requirements: ['_locale' => 'en|pl'],
        defaults: ['_locale' => 'en']
    )]
    #[IsGranted('ROLE_ADMIN')]
    public function edit(Request $request, int $id): Response
    {
        $article = $this->articleRepository->findWithCategory($id);
        if (!$article) {
            throw $this->createNotFoundException($this->translator->trans('article.not_found'));
        }

        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $article->setUpdatedAt(new \DateTimeImmutable());
            $this->entityManager->flush();

            $this->addFlash('success', $this->translator->trans('article.updated_successfully'));

            return $this->redirectToRoute('app_article_show', ['id' => $article->getId()]);
        }

        return $this->render('article/edit.html.twig', [
            'article' => $article,
            'form' => $form,
        ]);
    }

    /**
     * Show a confirmation form before deleting an article.
     *
     * @param int $id The article ID
     *
     * @return Response HTTP response with delete confirmation
     */
    #[\Symfony\Component\Routing\Attribute\Route(
        '/{_locale}/articles/{id}/delete',
        name: 'app_article_delete_confirm',
        methods: ['GET'],
        requirements: ['_locale' => 'en|pl'],
        defaults: ['_locale' => 'en']
    )]
    #[IsGranted('ROLE_ADMIN')]
    public function deleteConfirm(int $id): Response
    {
        $article = $this->articleRepository->findWithCategory($id);
        if (!$article) {
            throw $this->createNotFoundException($this->translator->trans('article.not_found'));
        }

        return $this->render('article/delete_confirm.html.twig', [
            'article' => $article,
        ]);
    }

    /**
     * Delete an article after confirmation.
     *
     * @param Request $request HTTP request
     * @param int     $id      The article ID
     *
     * @return Response HTTP response with redirect
     */
    #[\Symfony\Component\Routing\Attribute\Route(
        '/{_locale}/articles/{id}/delete',
        name: 'app_article_delete',
        methods: ['POST'],
        requirements: ['_locale' => 'en|pl'],
        defaults: ['_locale' => 'en']
    )]
    #[IsGranted('ROLE_ADMIN')]
    public function delete(Request $request, int $id): Response
    {
        $article = $this->articleRepository->findWithCategory($id);
        if (!$article) {
            throw $this->createNotFoundException($this->translator->trans('article.not_found'));
        }

        if ($this->isCsrfTokenValid('delete'.$article->getId(), (string) $request->request->get('_token'))) {
            $this->entityManager->remove($article);
            $this->entityManager->flush();

            $this->addFlash('success', $this->translator->trans('article.deleted_successfully'));
        }

        return $this->redirectToRoute('app_article_index');
    }
}
