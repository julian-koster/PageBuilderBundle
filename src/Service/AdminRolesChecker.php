<?php

namespace JulianKoster\PageBuilderBundle\Service;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bundle\SecurityBundle\Security;

readonly class AdminRolesChecker
{
    public function __construct(private ParameterBagInterface $parameterBag, private Security $security)
    {
    }

    private function getRoles(): array
    {
        if($this->parameterBag->has('page_builder.admin_roles')) {
            return $this->parameterBag->get('page_builder.admin_roles');
        }
        else {
            throw new \LogicException('No admin roles defined in the PageBuilderBundle configuration.');
        }
    }

    public function checkRoles(): bool
    {
        $user = $this->security->getUser();

        if(!$user instanceof UserInterface) {
            throw new AccessDeniedHttpException('User is not authenticated whilst trying to access the admin section of the PageBuilderBundle.');
        }

        $roles = $this->getRoles();

        foreach($roles as $role) {
            if($this->security->isGranted($role)) {
                return true;
            }
        }

        return false;
    }
}