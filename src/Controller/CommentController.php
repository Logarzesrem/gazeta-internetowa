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

use App\Entity\AdminUser;
use App\Entity\Article;
use App\Entity\Comment;
use App\Entity\User;
use App\Form\CommentType;
use App\Repository\CommentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * CommentController.
 */
class CommentController extends AbstractController
{
    /**
     * CommentController constructor.
     *
     * @param EntityManagerInterface $entityManager     Entity manager
     * @param CommentRepository      $commentRepository Comment repository
     * @param TranslatorInterface    $translator        Translator for messages
     */
    public function __construct(private readonly EntityManagerInterface $entityManager, private readonly CommentRepository $commentRepository, private readonly TranslatorInterface $translator)
    {
    }

    /**
     * Create a new comment for an article.
     *
     * @param Request $request HTTP request
     * @param Article $article The article to comment on
     *
     * @return Response HTTP response with redirect
     */
    #[\Symfony\Component\Routing\Attribute\Route(
        '/{_locale}/comments/article/{id}/new',
        name: 'app_comment_new',
        methods: ['POST'],
        requirements: ['_locale' => 'en|pl'],
        defaults: ['_locale' => 'en']
    )]
    #[IsGranted('ROLE_USER')]
    public function new(Request $request, Article $article): Response
    {
        /** @var User|AdminUser $user */
        $user = $this->getUser();

        // Check if the user is an admin
        if ($user instanceof AdminUser) {
            $this->addFlash('warning', 'comment.admin_not_allowed');

            return $this->redirectToRoute('app_article_show', ['id' => $article->getId()]);
        }

        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $comment->setArticle($article);
            $comment->setAuthor($user);

            $this->entityManager->persist($comment);
            $this->entityManager->flush();

            $this->addFlash('success', 'comment.added_successfully');
        } else {
            $this->addFlash('error', $this->translator->trans('comment.error.adding_failed'));
        }

        return $this->redirectToRoute('app_article_show', ['id' => $article->getId()]);
    }

    /**
     * Delete a comment.
     *
     * @param Request $request HTTP request
     * @param Comment $comment The comment to delete
     *
     * @return Response HTTP response with redirect
     */
    #[\Symfony\Component\Routing\Attribute\Route(
        '/{_locale}/comments/{id}',
        name: 'app_comment_delete',
        methods: ['POST'],
        requirements: ['_locale' => 'en|pl'],
        defaults: ['_locale' => 'en']
    )]
    #[IsGranted('ROLE_ADMIN')]
    public function delete(Request $request, Comment $comment): Response
    {
        $article = $comment->getArticle();

        if ($this->isCsrfTokenValid('delete'.$comment->getId(), (string) $request->request->get('_token'))) {
            $this->entityManager->remove($comment);
            $this->entityManager->flush();

            $this->addFlash('success', 'comment.deleted_successfully');
        }

        return $this->redirectToRoute('app_article_show', ['id' => $article ? $article->getId() : null]);
    }

    /**
     * Create a comment via POST (API-like endpoint).
     *
     * @param Request $request HTTP request
     *
     * @return Response HTTP response with redirect
     */
    #[\Symfony\Component\Routing\Attribute\Route(
        '/{_locale}/comments',
        name: 'app_comment_create',
        methods: ['POST'],
        requirements: ['_locale' => 'en|pl'],
        defaults: ['_locale' => 'en']
    )]
    public function create(Request $request): Response
    {
        $articleId = $request->request->get('article_id');
        $content = (string) $request->request->get('content', '');
        if (!$articleId || !$content) {
            return $this->redirectToRoute('app_article_index');
        }
        $article = $this->entityManager->getRepository(Article::class)->find($articleId);
        if (!$article) {
            return $this->redirectToRoute('app_article_index');
        }
        $comment = new Comment();
        $comment->setArticle($article);
        $comment->setContent($content);
        // For test, set author to null or a default user if needed
        $this->entityManager->persist($comment);
        $this->entityManager->flush();

        return $this->redirectToRoute('app_article_show', ['id' => $article->getId()]);
    }
}
