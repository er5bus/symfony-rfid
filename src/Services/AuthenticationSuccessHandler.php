<?php


namespace App\Services;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;

class AuthenticationSuccessHandler implements AuthenticationSuccessHandlerInterface
{
    /**
     * @var RouterInterface
     */
    protected $router;

    /**
     * @var AuthorizationCheckerInterface
     */
    protected $security;

    /**
     * Constructor
     *
     * @param RouterInterface $router
     * @param AuthorizationCheckerInterface $security
     */
    public function __construct(RouterInterface $router, AuthorizationCheckerInterface $security)
    {
        $this->router = $router;
        $this->security = $security;
    }

    /**
     * {@inheritDoc}
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token)
    {
        if ($this->security->isGranted('ROLE_USER')) {
            return new RedirectResponse($this->router->generate('book_shop'));
        }

        return new RedirectResponse($this->router->generate('dashboard_index'));
    }
}