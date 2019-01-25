<?php

namespace App\EventSubscriber;

use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class UserEnvironmentEventSubscriber
{
    public $environment;
    private $securityContext;

    public function __construct(TokenStorageInterface $securityContext = null, $environment)
    {
        $this->securityContext = $securityContext;
        $this->environment = $environment;
    }

    /**
     * @return bool
     */
    public function isCorrectEnvironment()
    {
        return
            'cli' !== PHP_SAPI ||
            ('cli' === PHP_SAPI && $this->environment === 'test')
            ;
    }

    /**
     * Gets the user Id.
     *
     * @return int | null $user_id The User Primary Key
     */
    public function getUserId()
    {
        $user = $this->getUser();

        if (!is_object($user)) {
            return 1;
        }

        return  $user->getId();
//        return  1;
    }

    public function getUser()
    {
        if ($this->environment === 'test') {
            return 1;
        }
        // @codeCoverageIgnoreStart
        if (PHP_SAPI === 'cli') {
            return 1;
        }

        if ($this->securityContext === null) {
            return 1;
        }

        $token = $this->securityContext->getToken();

        if ($token === null) {
            return 1;
        }

        return $token->getUser();
    }

    public function getAuditUserId()
    {
        if ($this->environment === 'test') {
            return [1, 'test'];
        }
        // @codeCoverageIgnoreStart
        if (PHP_SAPI === 'cli') {
            return [1, 'cli'];
        }

        if ($this->securityContext === null) {
            return [1, 'no-security'];
        }

        $token = $this->securityContext->getToken();

        if ($token === null) {
            return ['1', 'register'];
        }

        if (!is_object($token->getUser())) {
            return ['1', 'register'];
        }

        return (null !== $token) ? [$token->getUser()->getId(), $token->getUser()->getUsername()] : null;
        // @codeCoverageIgnoreEnd
    }
}
