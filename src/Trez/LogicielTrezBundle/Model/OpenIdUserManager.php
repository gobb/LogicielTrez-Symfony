<?php

namespace Trez\LogicielTrezBundle\Model;

use Fp\OpenIdBundle\Model\UserManager;
use Fp\OpenIdBundle\Model\IdentityManagerInterface;
use Doctrine\ORM\EntityManager;
use Trez\LogicielTrezBundle\Entity\OpenIdIdentity;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Security\Core\Exception\InsufficientAuthenticationException;

class OpenIdUserManager extends UserManager
{
    private $entityManager;

    public function __construct(IdentityManagerInterface $identityManager, EntityManager $entityManager)
    {
        parent::__construct($identityManager);

        $this->entityManager = $entityManager;
    }

    public function createUserFromIdentity($identity, array $attributes = array())
    {
        if (isset($attributes['contact/email']) === false) {
            throw new \Exception('Le provider ne nous a pas fourni votre adresse e-mail !');
        }
        $user = $this->entityManager->getRepository('TrezLogicielTrezBundle:User')->findOneBy(array(
            'mail' => $attributes['contact/email']
        ));

        if ($user === null) {
            throw new BadCredentialsException('Cet utilisateur ne semble pas être autorisé');
        }

        if ($user->getCanOpenId() === false) {
            throw new InsufficientAuthenticationException('Cet utilisateur ne supporte pas cette méthode de login');
        }

        // we create an OpenIdIdentity using this user
        $openIdIdentity = new OpenIdIdentity();
        $openIdIdentity->setIdentity($identity);
        $openIdIdentity->setAttributes($attributes);
        $openIdIdentity->setUser($user);

        $this->entityManager->persist($openIdIdentity);
        $this->entityManager->flush();

        return $user; // must always return UserInterface instance or throw an exception.
    }

    protected function loadUserByIdentity($identity)
    {
        $user = parent::loadUserByIdentity($identity);

        if ($user->getCanOpenId() === false) {
            throw new InsufficientAuthenticationException('Cet utilisateur ne supporte pas cette méthode de login');
        }

        return $user;
    }
}
