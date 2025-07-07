<?php

/**
 * User Controller.
 *
 * @author Konrad Stomski <konrad.stomski@student.uj.edu.pl>
 *
 * @copyright 2025 Konrad Stomski
 *
 * @license MIT
 */

namespace App\Controller;

use App\Entity\User;
use App\Form\LoginType;
use App\Form\UserPasswordChangeType;
use App\Form\UserProfileType;
use App\Form\UserRegistrationType;
use App\Service\Interface\UserServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Controller for handling user-related actions.
 */
class UserController extends AbstractController
{
    /**
     * Constructor.
     *
     * @param UserServiceInterface  $userService  User service
     * @param RequestStack          $requestStack Request stack
     * @param TokenStorageInterface $tokenStorage Token storage
     * @param TranslatorInterface   $translator   Translator
     */
    public function __construct(private readonly UserServiceInterface $userService, private readonly RequestStack $requestStack, private readonly TokenStorageInterface $tokenStorage, private readonly TranslatorInterface $translator)
    {
    }

    /**
     * User registration.
     *
     * @param Request $request HTTP request
     *
     * @return Response HTTP response
     */
    #[\Symfony\Component\Routing\Attribute\Route(
        '/{_locale}/register',
        name: 'app_user_register',
        methods: ['GET', 'POST'],
        requirements: ['_locale' => 'en|pl'],
        defaults: ['_locale' => 'en']
    )]
    public function register(Request $request): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('app_article_index');
        }

        $user = new User();
        $form = $this->createForm(UserRegistrationType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $this->userService->create($user);
                $this->addFlash('success', $this->translator->trans('user.account.created_successfully'));

                return $this->redirectToRoute('app_user_login');
            } catch (\InvalidArgumentException $e) {
                $this->addFlash('error', $e->getMessage());

                return $this->render('user/register.html.twig', [
                    'form' => $form->createView(),
                ]);
            }
        }

        return $this->render('user/register.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * User login.
     *
     * @param AuthenticationUtils $authenticationUtils Authentication utils
     *
     * @return Response HTTP response
     */
    #[\Symfony\Component\Routing\Attribute\Route(
        '/{_locale}/user/login',
        name: 'app_user_login',
        methods: ['GET', 'POST'],
        requirements: ['_locale' => 'en|pl'],
        defaults: ['_locale' => 'en']
    )]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        $form = $this->createForm(LoginType::class);

        return $this->render('user/login.html.twig', [
            'form' => $form->createView(),
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }

    /**
     * User profile.
     *
     * @return Response HTTP response
     */
    #[\Symfony\Component\Routing\Attribute\Route(
        '/{_locale}/profile',
        name: 'app_user_profile',
        methods: ['GET'],
        requirements: ['_locale' => 'en|pl'],
        defaults: ['_locale' => 'en']
    )]
    #[IsGranted('ROLE_USER')]
    public function profile(): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        return $this->render('user/profile.html.twig', [
            'user' => $user,
        ]);
    }

    /**
     * Edit user profile.
     *
     * @param Request $request HTTP request
     *
     * @return Response HTTP response
     */
    #[\Symfony\Component\Routing\Attribute\Route(
        '/{_locale}/profile/edit',
        name: 'app_user_profile_edit',
        methods: ['GET', 'POST'],
        requirements: ['_locale' => 'en|pl'],
        defaults: ['_locale' => 'en']
    )]
    #[IsGranted('ROLE_USER')]
    public function editProfile(Request $request): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        $form = $this->createForm(UserProfileType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $this->userService->update($user);
                $this->addFlash('success', $this->translator->trans('user.profile.updated_successfully'));

                return $this->redirectToRoute('app_user_profile');
            } catch (\InvalidArgumentException $e) {
                $this->addFlash('error', $e->getMessage());

                return $this->render('user/edit_profile.html.twig', [
                    'form' => $form->createView(),
                ]);
            }
        }

        return $this->render('user/edit_profile.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * Change user password.
     *
     * @param Request $request HTTP request
     *
     * @return Response HTTP response
     */
    #[\Symfony\Component\Routing\Attribute\Route(
        '/{_locale}/profile/change-password',
        name: 'app_user_change_password',
        methods: ['GET', 'POST'],
        requirements: ['_locale' => 'en|pl'],
        defaults: ['_locale' => 'en']
    )]
    #[IsGranted('ROLE_USER')]
    public function changePassword(Request $request): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        $form = $this->createForm(UserPasswordChangeType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $plainPassword = $user->getPlainPassword();
            if ($plainPassword) {
                $this->userService->changePassword($user, $plainPassword);
                $this->addFlash('success', $this->translator->trans('user.password.changed_successfully'));

                return $this->redirectToRoute('app_user_profile');
            }

            $this->addFlash('error', $this->translator->trans('user.password.required'));
        }

        return $this->render('user/change_password.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * User logout.
     *
     * @return Response HTTP response
     */
    #[\Symfony\Component\Routing\Attribute\Route(
        '/user/logout',
        name: 'app_user_logout'
    )]
    public function logout(): Response
    {
        // This method can be blank - it will be intercepted by the logout key on the firewall
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on the firewall');
    }

    /**
     * Legacy logout route for backward compatibility.
     *
     * @return Response HTTP response
     */
    #[\Symfony\Component\Routing\Attribute\Route(
        '/{_locale}/user/logout',
        name: 'app_user_logout_legacy',
        requirements: ['_locale' => 'en|pl'],
        defaults: ['_locale' => 'en']
    )]
    public function logoutLegacy(): Response
    {
        // Handle logout manually for legacy route
        $this->tokenStorage->setToken(null);
        $this->requestStack->getSession()->invalidate();

        // Redirect to the target page
        return $this->redirectToRoute('app_article_index');
    }
}
