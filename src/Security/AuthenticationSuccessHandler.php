<?php

/**
 * AuthenticationSuccessHandler.
 *
 * @author Konrad Stomski <konrad.stomski@student.uj.edu.pl>
 *
 * @copyright 2025 Konrad Stomski
 *
 * @license MIT
 */

declare(strict_types=1);

namespace App\Security;

use App\Entity\AdminUser;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;

/**
 * Custom authentication success handler that redirects users based on their role.
 */
class AuthenticationSuccessHandler implements AuthenticationSuccessHandlerInterface
{
    /**
     * @param UrlGeneratorInterface $urlGenerator The URL generator service
     */
    public function __construct(private readonly UrlGeneratorInterface $urlGenerator)
    {
    }

    /**
     * Handle successful authentication by redirecting users to appropriate pages.
     *
     * @param Request        $request The request object
     * @param TokenInterface $token   The security token
     *
     * @return Response The redirect response
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token): Response
    {
        $user = $token->getUser();

        // If user is an admin, redirect to admin dashboard
        if ($user && ($user instanceof AdminUser || in_array('ROLE_ADMIN', $user->getRoles()))) {
            return new RedirectResponse($this->urlGenerator->generate('admin_dashboard'));
        }

        // For regular users, redirect to articles page
        return new RedirectResponse($this->urlGenerator->generate('app_article_index'));
    }
}
