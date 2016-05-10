<?php

namespace ProxyManager\AopBundle;

use ProxyManager\AopBundle\DependencyInjection\CompilerPass\AopCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * @author  Urban Soban <urban.soban@dlabs.si>
 * @package ProxyManager\AopBundle
 */
class ProxyManagerAopBundle extends Bundle
{
    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new AopCompilerPass());
    }
}
