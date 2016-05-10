<?php

namespace ProxyManager\AopBundle\Aop\Aspect;

use ProxyManager\AopBundle\Aop\Advice\BeforeAdviceInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

/**
 * @author  Urban Soban <urban.soban@dlabs.si>
 * @package ProxyManager\AopBundle\Aop\Aspect
 */
class AuthorizationCheckerAspect implements BeforeAdviceInterface
{
    /** @var AuthorizationCheckerInterface */
    private $authChecker;

    /**
     * @param AuthorizationCheckerInterface $authChecker
     */
    public function __construct(AuthorizationCheckerInterface $authChecker)
    {
        $this->authChecker = $authChecker;
    }

    /**
     * @param $config
     *
     * @return \Closure
     */
    public function before(array $config)
    {
        $requiredRole = $config['role'];

        /**
         * @param object $proxy
         * @param object $instance
         *
         * @param string $method      name of the called method
         * @param array  $params      sorted array of parameters passed to the intercepted
         *                            method, indexed by parameter name
         * @param bool   $returnEarly flag to tell the interceptor proxy to return early, returning
         *                            the interceptor's return value instead of executing the method logic
         *
         * @return void
         * @throws \Exception
         */
        return function ($proxy, $instance, $method, $params, &$returnEarly) use ($requiredRole) {
            if (!$this->authChecker->isGranted($requiredRole)) {
                throw new \Exception('Not authorized.');
            }
        };
    }
}