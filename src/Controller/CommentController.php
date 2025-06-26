<?php

declare(strict_types=1);

namespace App\Controller;

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

class CommentController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly CommentRepository $commentRepository,
    ) {
    }

    #[\Symfony\Component\Routing\Attribute\Route('/{_locale}/comments/article/{id}/new', name: 'app_comment_new', methods: ['POST'], requirements: ['_locale' => 'en|pl'], defaults: ['_locale' => 'en'])]
    #[IsGranted('ROLE_USER')]
    public function new(Request $request, Article $article): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $comment->setArticle($article);
            $comment->setAuthor($user);

            $this->entityManager->persist($comment);
            $this->entityManager->flush();

            $this->addFlash('success', 'Comment added successfully.');
        } else {
            $this->addFlash('error', 'There was an error adding your comment. Please try again.');
        }

        return $this->redirectToRoute('app_article_show', ['id' => $article->getId()]);
    }

    #[\Symfony\Component\Routing\Attribute\Route('/{_locale}/comments/{id}', name: 'app_comment_delete', methods: ['POST'], requirements: ['_locale' => 'en|pl'], defaults: ['_locale' => 'en'])]
    #[IsGranted('ROLE_ADMIN')]
    public function delete(Request $request, Comment $comment): Response
    {
        $article = $comment->getArticle();

        if ($this->isCsrfTokenValid('delete' . $comment->getId(), $request->request->get('_token'))) {
            $this->entityManager->remove($comment);
            $this->entityManager->flush();

            $this->addFlash('success', 'Comment deleted successfully.');
        }

        return $this->redirectToRoute('app_article_show', ['id' => $article->getId()]);
    }

    #[\Symfony\Component\Routing\Attribute\Route('/{_locale}/comments', name: 'app_comment_create', methods: ['POST'], requirements: ['_locale' => 'en|pl'], defaults: ['_locale' => 'en'])]
    public function create(Request $request): Response
    {
        $articleId = $request->request->get('article_id');
        $content = $request->request->get('content', '');
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
