<?php

namespace ProxyManager\AopBundle\Aop;

use ProxyManager\AopBundle\Aop\Advice\AfterAdviceInterface;
use ProxyManager\AopBundle\Aop\Advice\BeforeAdviceInterface;
use ProxyManager\AopBundle\Aop\Aspect\AspectInterface;
use ProxyManager\Factory\AccessInterceptorScopeLocalizerFactory;

/**
 * @author  Urban Soban <urban.soban@dlabs.si>
 * @package ProxyManager\AopBundle\Aop\Interceptor
 */
class ProxyFactory
{
    /** @var AccessInterceptorScopeLocalizerFactory */
    protected $interceptorFactory;

    /**
     * @param AccessInterceptorScopeLocalizerFactory $factory
     */
    public function __construct(AccessInterceptorScopeLocalizerFactory $factory)
    {
        $this->interceptorFactory = $factory;
    }

    /**
     * @param mixed $instance
     * @param array $methods
     *
     * @return \ProxyManager\Proxy\AccessInterceptorInterface
     *
     */
    public function createProxy($instance, array $methods)
    {
        // First, fetch aspect advices and group them into `before` and `after` groups.
        $beforeAdvices = [];
        $afterAdvices  = [];
        foreach ($methods as $methodName => $aspectConfigs) {
            foreach ($aspectConfigs as $aspectConfig) {
                $aspect = $aspectConfig['aspect'];

                if ($aspect instanceof BeforeAdviceInterface) {
                    $beforeAdvices[$methodName][] = $aspect->before($aspectConfig['config']);
                }
                if ($aspect instanceof AfterAdviceInterface) {
                    $afterAdvices[$methodName][] = $aspect->after($aspectConfig['config']);
                }
            }
        }

        $before = [];
        $after  = [];
        foreach ($beforeAdvices as $methodName => $advices) {
            $before[$methodName] = function () use ($advices) {
                foreach ($advices as $advice) {
                    call_user_func_array($advice, func_get_args());
                }
            };
        }
        foreach ($afterAdvices as $methodName => $advices) {
            $after[$methodName] = function () use ($advices) {
                foreach ($advices as $advice) {
                    call_user_func_array($advice, func_get_args());
                }
            };
        }

        return $this->interceptorFactory->createProxy($instance, $before, $after);
    }
}