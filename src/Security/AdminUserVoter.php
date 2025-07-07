<?php

/**
 * AdminUserVoter.
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
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

/**
 * Voter for AdminUser permissions.
 */
class AdminUserVoter extends Voter
{
    public const VIEW = 'view';
    public const EDIT = 'edit';
    public const DELETE = 'delete';

    /**
     * Determines if this voter supports the given attribute and subject.
     *
     * @param string $attribute The permission attribute
     * @param mixed  $subject   The subject to check permissions for
     *
     * @return bool True if this voter supports the attribute and subject
     */
    protected function supports(string $attribute, mixed $subject): bool
    {
        if (!in_array($attribute, [self::VIEW, self::EDIT, self::DELETE])) {
            return false;
        }

        if (!$subject instanceof AdminUser) {
            return false;
        }

        return true;
    }

    /**
     * Determines if the current user has the requested permission.
     *
     * @param string         $attribute The permission attribute
     * @param mixed          $subject   The subject to check permissions for
     * @param TokenInterface $token     The security token
     *
     * @return bool True if the user has the requested permission
     */
    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof AdminUser) {
            return false;
        }

        /** @var AdminUser $adminUser */
        $adminUser = $subject;

        return match ($attribute) {
            self::VIEW => true,
            self::EDIT => $this->canEdit($adminUser, $user),
            self::DELETE => $this->canDelete($adminUser, $user),
            default => throw new \LogicException('This code should not be reached!'),
        };
    }

    /**
     * Determines if the current user can edit the given admin user.
     *
     * @param AdminUser $adminUser   The admin user to edit
     * @param AdminUser $currentUser The current user
     *
     * @return bool True if the user can edit the admin user
     */
    private function canEdit(AdminUser $adminUser, AdminUser $currentUser): bool
    {
        // Users can edit their own profile
        if ($adminUser->getId() === $currentUser->getId()) {
            return true;
        }

        // All admins can edit other admins
        return true;
    }

    /**
     * Determines if the current user can delete the given admin user.
     *
     * @param AdminUser $adminUser   The admin user to delete
     * @param AdminUser $currentUser The current user
     *
     * @return bool True if the user can delete the admin user
     */
    private function canDelete(AdminUser $adminUser, AdminUser $currentUser): bool
    {
        // Users cannot delete themselves
        if ($adminUser->getId() === $currentUser->getId()) {
            return false;
        }

        // All admins can delete other admins
        return true;
    }
}
