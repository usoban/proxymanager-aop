<?php

namespace ProxyManager\AopBundle\Aop\Aspect;

use ProxyManager\AopBundle\Aop\Advice\AfterAdviceInterface;
use ProxyManager\AopBundle\Aop\Advice\BeforeAdviceInterface;

/**
 * @author  Urban Soban <urban.soban@dlabs.si>
 * @package ProxyManager\AopBundle\Aop\Aspect
 */

class TestAspect implements BeforeAdviceInterface, AfterAdviceInterface
{
    /**
     * {@inheritdoc}
     */
    public function before(array $config)
    {
        return function () {
            var_dump("BEFORE!");
        };
    }

    /**
     * {@inheritdoc}
     */
    public function after(array $config)
    {
        return function () {
            var_dump("AFTER!");
        };
    }
}
