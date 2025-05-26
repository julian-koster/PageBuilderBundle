<?php

namespace Service;

use JulianKoster\PageBuilderBundle\Service\AdminRolesChecker;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class AdminRolesCheckerTest extends TestCase
{
    public function testAnonymousDoesNotHaveAccess(): void
    {
        $security = $this->createMock(Security::class);
        $user = $this->createMock(UserInterface::class);

        $security->method('getUser')->willReturn($user);
        $security->method('isGranted')->with('ROLE_ADMIN')->willReturn(false);

        $parameterBag = $this->createMock(ParameterBagInterface::class);
        $parameterBag->method('has')->with('page_builder.admin_roles')->willReturn(true);
        $parameterBag->method('get')->with('page_builder.admin_roles')->willReturn(['ROLE_ADMIN']);

        $checker = new AdminRolesChecker($parameterBag, $security);

        $this->assertFalse($checker->checkRoles());
    }

    public function testValidRolesHaveAccess(): void
    {
        $security = $this->createMock(Security::class);
        $user = $this->createMock(UserInterface::class);

        $security->method('getUser')->willReturn($user);
        $security->method('isGranted')->with('ROLE_ADMIN')->willReturn(true);

        $parameterBag = $this->createMock(ParameterBagInterface::class);
        $parameterBag->method('has')->with('page_builder.admin_roles')->willReturn(true);
        $parameterBag->method('get')->with('page_builder.admin_roles')->willReturn(['ROLE_ADMIN']);

        $checker = new AdminRolesChecker($parameterBag, $security);
        $this->assertTrue($checker->checkRoles());
    }
}