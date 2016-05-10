<?php

namespace ProxyManager\AopBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class ProxyManagerAopExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('parameters.yml');
        $loader->load('services.yml');

        $this->loadAOPConfig($container, $config);
    }

    /**
     * Configures the bundle.
     *
     * @param ContainerBuilder $container
     * @param array            $config
     */
    public function loadAOPConfig(ContainerBuilder $container, array $config)
    {
        $defaultConfig = $container->getParameter(Configuration::ASPECTS_LIST);

        if (isset($config['aspects']) && !empty($config['aspects'])) {
            $container->setParameter(
                Configuration::ASPECTS_LIST,
                array_merge_recursive($defaultConfig, $config['aspects'])
            );
        }
    }
}
