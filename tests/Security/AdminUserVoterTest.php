<?php

declare(strict_types=1);

namespace App\Tests\Security;

use App\Entity\AdminUser;
use App\Entity\User;
use App\Security\AdminUserVoter;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class AdminUserVoterTest extends TestCase
{
    private AdminUserVoter $voter;
    private TokenInterface $token;

    protected function setUp(): void
    {
        $this->voter = new AdminUserVoter();
        $this->token = $this->createMock(TokenInterface::class);
    }

    public function testSupportsWithValidAttributeAndSubject(): void
    {
        $adminUser = new AdminUser();
        $adminUser->setEmail('admin@example.com');

        $result = $this->voter->vote($this->token, $adminUser, [AdminUserVoter::VIEW]);
        $this->assertNotEquals(AdminUserVoter::ACCESS_ABSTAIN, $result);

        $result = $this->voter->vote($this->token, $adminUser, [AdminUserVoter::EDIT]);
        $this->assertNotEquals(AdminUserVoter::ACCESS_ABSTAIN, $result);

        $result = $this->voter->vote($this->token, $adminUser, [AdminUserVoter::DELETE]);
        $this->assertNotEquals(AdminUserVoter::ACCESS_ABSTAIN, $result);
    }

    public function testSupportsWithInvalidAttribute(): void
    {
        $adminUser = new AdminUser();
        $adminUser->setEmail('admin@example.com');

        $result = $this->voter->vote($this->token, $adminUser, ['invalid']);
        $this->assertEquals(AdminUserVoter::ACCESS_ABSTAIN, $result);

        $result = $this->voter->vote($this->token, $adminUser, ['create']);
        $this->assertEquals(AdminUserVoter::ACCESS_ABSTAIN, $result);
    }

    public function testSupportsWithInvalidSubject(): void
    {
        $user = new User();
        $user->setEmail('user@example.com');

        $result = $this->voter->vote($this->token, $user, [AdminUserVoter::VIEW]);
        $this->assertEquals(AdminUserVoter::ACCESS_ABSTAIN, $result);

        $result = $this->voter->vote($this->token, null, [AdminUserVoter::EDIT]);
        $this->assertEquals(AdminUserVoter::ACCESS_ABSTAIN, $result);

        $result = $this->voter->vote($this->token, 'string', [AdminUserVoter::DELETE]);
        $this->assertEquals(AdminUserVoter::ACCESS_ABSTAIN, $result);
    }

    public function testVoteWithNonAdminUser(): void
    {
        $adminUser = new AdminUser();
        $adminUser->setEmail('admin@example.com');

        $regularUser = new User();
        $regularUser->setEmail('user@example.com');

        $this->token
            ->expects($this->once())
            ->method('getUser')
            ->willReturn($regularUser);

        $result = $this->voter->vote($this->token, $adminUser, [AdminUserVoter::VIEW]);
        $this->assertEquals(AdminUserVoter::ACCESS_DENIED, $result);
    }

    public function testVoteView(): void
    {
        $adminUser = new AdminUser();
        $adminUser->setEmail('admin1@example.com');

        $currentUser = new AdminUser();
        $currentUser->setEmail('admin2@example.com');

        $this->token
            ->expects($this->once())
            ->method('getUser')
            ->willReturn($currentUser);

        $result = $this->voter->vote($this->token, $adminUser, [AdminUserVoter::VIEW]);
        $this->assertEquals(AdminUserVoter::ACCESS_GRANTED, $result);
    }

    public function testVoteEditOwnProfile(): void
    {
        $adminUser = new AdminUser();
        $adminUser->setEmail('admin@example.com');
        // Set same ID to simulate same user
        $reflection = new \ReflectionClass($adminUser);
        $idProperty = $reflection->getProperty('id');
        $idProperty->setAccessible(true);
        $idProperty->setValue($adminUser, 1);

        $currentUser = new AdminUser();
        $currentUser->setEmail('admin@example.com'); // Same email
        // Set same ID to simulate same user
        $reflection = new \ReflectionClass($currentUser);
        $idProperty = $reflection->getProperty('id');
        $idProperty->setAccessible(true);
        $idProperty->setValue($currentUser, 1);

        $this->token
            ->expects($this->once())
            ->method('getUser')
            ->willReturn($currentUser);

        $result = $this->voter->vote($this->token, $adminUser, [AdminUserVoter::EDIT]);
        $this->assertEquals(AdminUserVoter::ACCESS_GRANTED, $result);
    }

    public function testVoteEditOtherProfile(): void
    {
        $adminUser = new AdminUser();
        $adminUser->setEmail('admin1@example.com');
        // Set different ID to simulate different users
        $reflection = new \ReflectionClass($adminUser);
        $idProperty = $reflection->getProperty('id');
        $idProperty->setAccessible(true);
        $idProperty->setValue($adminUser, 1);

        $currentUser = new AdminUser();
        $currentUser->setEmail('admin2@example.com'); // Different email
        // Set different ID to simulate different users
        $reflection = new \ReflectionClass($currentUser);
        $idProperty = $reflection->getProperty('id');
        $idProperty->setAccessible(true);
        $idProperty->setValue($currentUser, 2);

        $this->token
            ->expects($this->once())
            ->method('getUser')
            ->willReturn($currentUser);

        $result = $this->voter->vote($this->token, $adminUser, [AdminUserVoter::EDIT]);
        $this->assertEquals(AdminUserVoter::ACCESS_GRANTED, $result);
    }

    public function testVoteDeleteOwnProfile(): void
    {
        $adminUser = new AdminUser();
        $adminUser->setEmail('admin@example.com');
        // Set same ID to simulate same user
        $reflection = new \ReflectionClass($adminUser);
        $idProperty = $reflection->getProperty('id');
        $idProperty->setAccessible(true);
        $idProperty->setValue($adminUser, 1);

        $currentUser = new AdminUser();
        $currentUser->setEmail('admin@example.com'); // Same email
        // Set same ID to simulate same user
        $reflection = new \ReflectionClass($currentUser);
        $idProperty = $reflection->getProperty('id');
        $idProperty->setAccessible(true);
        $idProperty->setValue($currentUser, 1);

        $this->token
            ->expects($this->once())
            ->method('getUser')
            ->willReturn($currentUser);

        $result = $this->voter->vote($this->token, $adminUser, [AdminUserVoter::DELETE]);
        $this->assertEquals(AdminUserVoter::ACCESS_DENIED, $result);
    }

    public function testVoteDeleteOtherProfile(): void
    {
        $adminUser = new AdminUser();
        $adminUser->setEmail('admin1@example.com');
        // Set different ID to simulate different users
        $reflection = new \ReflectionClass($adminUser);
        $idProperty = $reflection->getProperty('id');
        $idProperty->setAccessible(true);
        $idProperty->setValue($adminUser, 1);

        $currentUser = new AdminUser();
        $currentUser->setEmail('admin2@example.com'); // Different email
        // Set different ID to simulate different users
        $reflection = new \ReflectionClass($currentUser);
        $idProperty = $reflection->getProperty('id');
        $idProperty->setAccessible(true);
        $idProperty->setValue($currentUser, 2);

        $this->token
            ->expects($this->once())
            ->method('getUser')
            ->willReturn($currentUser);

        $result = $this->voter->vote($this->token, $adminUser, [AdminUserVoter::DELETE]);
        $this->assertEquals(AdminUserVoter::ACCESS_GRANTED, $result);
    }

    public function testVoteWithUnsupportedAttribute(): void
    {
        $adminUser = new AdminUser();
        $adminUser->setEmail('admin@example.com');

        $currentUser = new AdminUser();
        $currentUser->setEmail('admin2@example.com');

        $this->token
            ->expects($this->never())
            ->method('getUser');

        $result = $this->voter->vote($this->token, $adminUser, ['invalid']);
        $this->assertEquals(AdminUserVoter::ACCESS_ABSTAIN, $result);
    }
}
