<?php

namespace ProxyManager\AopBundle\Aop\Advice;

/**
 * @author  Urban Soban <u.soban@gmail.com>
 * @package ProxyManager\AopBundle\Aop\Advice
 */

interface BeforeAdviceInterface
{
    /**
     * @param array $config
     *
     * @return \Closure
     */
    public function before(array $config);
}

