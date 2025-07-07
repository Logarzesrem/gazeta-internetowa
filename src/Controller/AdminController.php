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
use App\Entity\User;
use App\Form\AdminUserType;
use App\Repository\UserRepository;
use App\Service\AdminUserManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * AdminController.
 */
#[IsGranted('ROLE_ADMIN')]
class AdminController extends AbstractController
{
    /**
     * Constructor.
     *
     * @param AdminUserManager    $adminUserManager Admin user manager
     * @param TranslatorInterface $translator       Translator
     * @param UserRepository      $userRepository   Repository for regular users
     */
    public function __construct(private readonly AdminUserManager $adminUserManager, private readonly TranslatorInterface $translator, private readonly UserRepository $userRepository)
    {
    }

    /**
     * Admin dashboard with user listing.
     *
     * @param Request $request HTTP request
     *
     * @return Response HTTP response with rendered dashboard
     */
    #[\Symfony\Component\Routing\Attribute\Route(
        '/{_locale}/admin',
        name: 'admin_dashboard',
        methods: ['GET'],
        requirements: ['_locale' => 'en|pl'],
        defaults: ['_locale' => 'en']
    )]
    public function index(Request $request): Response
    {
        $page = max(1, $request->query->getInt('page', 1));
        $sortField = (string) $request->query->get('sort', 'name');
        $sortDirection = (string) $request->query->get('direction', 'asc');

        $users = $this->adminUserManager->getPaginatedAdminUsers(
            $page,
            10,
            $sortField,
            $sortDirection
        );

        return $this->render('admin/index.html.twig', [
            'users' => $users['items'],
            'total' => $users['total'],
            'page' => $page,
            'sortField' => $sortField,
            'sortDirection' => $sortDirection,
        ]);
    }

    /**
     * List all registered users.
     *
     * @param Request $request HTTP request
     *
     * @return Response HTTP response with rendered user list
     */
    #[\Symfony\Component\Routing\Attribute\Route(
        '/{_locale}/admin/users',
        name: 'admin_users_list',
        methods: ['GET'],
        requirements: ['_locale' => 'en|pl'],
        defaults: ['_locale' => 'en']
    )]
    public function listUsers(Request $request): Response
    {
        $page = max(1, $request->query->getInt('page', 1));
        $sortField = (string) $request->query->get('sort', 'createdAt');
        $sortDirection = (string) $request->query->get('direction', 'desc');

        // Get users from UserRepository
        $users = $this->userRepository->findPaginated($page, 10, $sortField, $sortDirection);

        return $this->render('admin/users.html.twig', [
            'users' => $users['items'],
            'total' => $users['total'],
            'page' => $page,
            'sortField' => $sortField,
            'sortDirection' => $sortDirection,
        ]);
    }

    /**
     * Create a new admin user.
     *
     * @param Request $request HTTP request
     *
     * @return Response HTTP response with form or redirect
     */
    #[\Symfony\Component\Routing\Attribute\Route(
        '/{_locale}/admin/users/new',
        name: 'admin_user_new',
        methods: ['GET', 'POST'],
        requirements: ['_locale' => 'en|pl'],
        defaults: ['_locale' => 'en']
    )]
    public function new(Request $request): Response
    {
        $user = new AdminUser();
        $form = $this->createForm(AdminUserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $this->adminUserManager->createAdminUser($user);
                $this->addFlash('success', $this->translator->trans('admin.flash.user_created', [], 'admin'));

                return $this->redirectToRoute('admin_dashboard');
            } catch (\Exception) {
                $this->addFlash('error', $this->translator->trans('admin.flash.user_create_error', [], 'admin'));
            }
        }

        return $this->render('admin/user/form.html.twig', [
            'form' => $form->createView(),
            'title' => 'admin.title.new_user',
            'is_admin' => true,
        ]);
    }

    /**
     * Edit an existing admin user.
     *
     * @param Request   $request HTTP request
     * @param AdminUser $user    The admin user to edit
     *
     * @return Response HTTP response with form or redirect
     */
    #[\Symfony\Component\Routing\Attribute\Route(
        '/{_locale}/admin/users/{id}/edit',
        name: 'admin_user_edit',
        methods: ['GET', 'POST'],
        requirements: ['_locale' => 'en|pl'],
        defaults: ['_locale' => 'en']
    )]
    public function edit(Request $request, AdminUser $user): Response
    {
        $this->denyAccessUnlessGranted('edit', $user);

        $form = $this->createForm(AdminUserType::class, $user, [
            'show_password' => false,
            'validation_groups' => ['Default'],
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $this->adminUserManager->updateAdminUser($user);
                $this->addFlash('success', $this->translator->trans('admin.flash.user_updated', [], 'admin'));

                return $this->redirectToRoute('admin_dashboard');
            } catch (\Exception) {
                $this->addFlash('error', $this->translator->trans('admin.flash.user_update_error', [], 'admin'));
            }
        }

        return $this->render('admin/user/form.html.twig', [
            'form' => $form->createView(),
            'title' => 'admin.title.edit_user',
            'user' => $user,
            'is_admin' => true,
        ]);
    }

    /**
     * Delete an admin user.
     *
     * @param Request   $request HTTP request
     * @param AdminUser $user    The admin user to delete
     *
     * @return Response HTTP response with redirect
     */
    #[\Symfony\Component\Routing\Attribute\Route(
        '/{_locale}/admin/users/{id}/delete-admin',
        name: 'admin_user_delete',
        methods: ['POST'],
        requirements: ['_locale' => 'en|pl'],
        defaults: ['_locale' => 'en']
    )]
    public function delete(Request $request, AdminUser $user): Response
    {
        $this->denyAccessUnlessGranted('delete', $user);

        if (!$this->isCsrfTokenValid('delete'.$user->getId(), (string) $request->request->get('_token'))) {
            throw $this->createAccessDeniedException();
        }

        try {
            $this->adminUserManager->deleteAdminUser($user);
            $this->addFlash('success', $this->translator->trans('admin.flash.user_deleted', [], 'admin'));
        } catch (\Exception) {
            $this->addFlash('error', $this->translator->trans('admin.flash.user_delete_error', [], 'admin'));
        }

        return $this->redirectToRoute('admin_dashboard');
    }

    /**
     * Change admin user password.
     *
     * @param Request   $request HTTP request
     * @param AdminUser $user    The admin user whose password is changed
     *
     * @return Response HTTP response with form or redirect
     */
    #[\Symfony\Component\Routing\Attribute\Route(
        '/{_locale}/admin/users/{id}/password',
        name: 'admin_user_password',
        methods: ['GET', 'POST'],
        requirements: ['_locale' => 'en|pl'],
        defaults: ['_locale' => 'en']
    )]
    public function changePassword(Request $request, AdminUser $user): Response
    {
        $this->denyAccessUnlessGranted('edit', $user);

        $form = $this->createForm(AdminUserType::class, $user, [
            'validation_groups' => ['password_change'],
            'show_password' => true,
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $this->adminUserManager->updateAdminUser($user);
                $this->addFlash('success', $this->translator->trans('admin.flash.password_updated', [], 'admin'));

                return $this->redirectToRoute('admin_dashboard');
            } catch (\Exception) {
                $this->addFlash('error', $this->translator->trans('admin.flash.password_update_error', [], 'admin'));
            }
        }

        return $this->render('admin/user/form.html.twig', [
            'form' => $form->createView(),
            'title' => 'admin.title.change_password',
            'user' => $user,
            'is_admin' => true,
        ]);
    }

    /**
     * View admin user details.
     *
     * @param int $id The admin user ID
     *
     * @return Response HTTP response with user details or not found
     */
    #[\Symfony\Component\Routing\Attribute\Route(
        '/{_locale}/admin/users/{id}/view',
        name: 'admin_user_view',
        methods: ['GET'],
        requirements: ['_locale' => 'en|pl'],
        defaults: ['_locale' => 'en']
    )]
    public function viewUser(int $id): Response
    {
        $user = $this->userRepository->find($id);
        if (!$user) {
            throw $this->createNotFoundException($this->translator->trans('admin.user_not_found', [], 'admin'));
        }

        return $this->render('admin/user/view.html.twig', [
            'user' => $user,
        ]);
    }

    /**
     * Edit a regular user (not admin).
     *
     * @param Request                $request HTTP request
     * @param int                    $id      The user ID
     * @param EntityManagerInterface $em      Entity manager
     *
     * @return Response HTTP response with form or redirect
     */
    #[\Symfony\Component\Routing\Attribute\Route(
        '/{_locale}/admin/users/{id}/edit-user',
        name: 'admin_regular_user_edit',
        methods: ['GET', 'POST'],
        requirements: ['_locale' => 'en|pl'],
        defaults: ['_locale' => 'en']
    )]
    public function editUser(Request $request, int $id, EntityManagerInterface $em): Response
    {
        $user = $this->userRepository->find($id);
        if (!$user) {
            throw $this->createNotFoundException($this->translator->trans('admin.user_not_found', [], 'admin'));
        }
        $form = $this->createForm(\App\Form\UserProfileType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            $this->addFlash('success', $this->translator->trans('admin.flash.user_updated', [], 'admin'));

            return $this->redirectToRoute('admin_users_list');
        }

        return $this->render('admin/user/form.html.twig', [
            'form' => $form->createView(),
            'user' => $user,
            'title' => $this->translator->trans('admin.title.edit_regular_user', [], 'admin'),
            'is_admin' => false,
        ]);
    }

    /**
     * Activate a regular user.
     *
     * @param int                    $id The user ID
     * @param EntityManagerInterface $em Entity manager
     *
     * @return Response HTTP response with redirect
     */
    #[\Symfony\Component\Routing\Attribute\Route(
        '/{_locale}/admin/users/{id}/activate',
        name: 'admin_user_activate',
        methods: ['POST'],
        requirements: ['_locale' => 'en|pl'],
        defaults: ['_locale' => 'en']
    )]
    public function activateUser(int $id, EntityManagerInterface $em): Response
    {
        $user = $this->userRepository->find($id);
        if (!$user) {
            throw $this->createNotFoundException($this->translator->trans('admin.user_not_found', [], 'admin'));
        }
        $user->setIsActive(true);
        $em->flush();
        $this->addFlash('success', $this->translator->trans('admin.flash.user_activated', [], 'admin'));

        return $this->redirectToRoute('admin_users_list');
    }

    /**
     * Deactivate a regular user.
     *
     * @param int                    $id The user ID
     * @param EntityManagerInterface $em Entity manager
     *
     * @return Response HTTP response with redirect
     */
    #[\Symfony\Component\Routing\Attribute\Route(
        '/{_locale}/admin/users/{id}/deactivate',
        name: 'admin_user_deactivate',
        methods: ['POST'],
        requirements: ['_locale' => 'en|pl'],
        defaults: ['_locale' => 'en']
    )]
    public function deactivateUser(int $id, EntityManagerInterface $em): Response
    {
        $user = $this->userRepository->find($id);
        if (!$user) {
            throw $this->createNotFoundException($this->translator->trans('admin.user_not_found', [], 'admin'));
        }
        $user->setIsActive(false);
        $em->flush();
        $this->addFlash('success', $this->translator->trans('admin.flash.user_deactivated', [], 'admin'));

        return $this->redirectToRoute('admin_users_list');
    }

    /**
     * Delete a regular user (not admin).
     *
     * @param int                    $id The user ID
     * @param EntityManagerInterface $em Entity manager
     *
     * @return Response HTTP response with redirect
     */
    #[\Symfony\Component\Routing\Attribute\Route(
        '/{_locale}/admin/users/{id}/delete',
        name: 'admin_regular_user_delete',
        methods: ['POST'],
        requirements: ['_locale' => 'en|pl'],
        defaults: ['_locale' => 'en']
    )]
    public function deleteUser(int $id, EntityManagerInterface $em): Response
    {
        $user = $this->userRepository->find($id);
        if (!$user) {
            throw $this->createNotFoundException($this->translator->trans('admin.user_not_found', [], 'admin'));
        }
        $em->remove($user);
        $em->flush();
        $this->addFlash('success', $this->translator->trans('admin.flash.user_deleted', [], 'admin'));

        return $this->redirectToRoute('admin_users_list');
    }

    /**
     * Show regular user details.
     *
     * @param int $id The user ID
     *
     * @return Response HTTP response with redirect or not found
     */
    #[\Symfony\Component\Routing\Attribute\Route(
        '/{_locale}/admin/users/{id}',
        name: 'admin_user_show',
        methods: ['GET'],
        requirements: ['_locale' => 'en|pl'],
        defaults: ['_locale' => 'en']
    )]
    public function showUser(int $id): Response
    {
        // First try to find as admin user
        $adminUser = $this->adminUserManager->getAdminUserById($id);
        if ($adminUser) {
            return $this->redirectToRoute('admin_user_edit', ['id' => $id]);
        }

        // If not admin user, try regular user
        $user = $this->userRepository->find($id);
        if ($user) {
            return $this->redirectToRoute('admin_user_view', ['id' => $id]);
        }

        throw $this->createNotFoundException($this->translator->trans('admin.user_not_found', [], 'admin'));
    }
}
