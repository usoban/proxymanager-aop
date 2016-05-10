<?php

namespace ProxyManager\AopBundle\DependencyInjection\CompilerPass;

use ProxyManager\AopBundle\DependencyInjection\Configuration;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @author  Urban Soban <urban.soban@dlabs.si>
 * @package ProxyManager\AopBundle\DependencyInjection\CompilerPass
 */
class AopCompilerPass implements CompilerPassInterface
{
    /** Service name of the proxy factory. */
    const PROXY_FACTORY = 'proxy_manager_aop.factory.proxy_factory';

    /**
     * @var Reference
     */
    protected $proxyFactory;

    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $aspectConfigs = $container->getParameter(Configuration::ASPECTS_LIST);
        $aspects       = [];

        foreach ($aspectConfigs as $aspectConfig) {
            $aspects[$aspectConfig['declaration']['name']] = $aspectConfig;
        }

        $this->enhance($container, $aspects);
    }

    /**
     * @param ContainerBuilder $container
     * @param array            $aspects
     */
    protected function enhance(ContainerBuilder $container, array $aspects)
    {
        $proxyFactory = new Reference(self::PROXY_FACTORY);
        $aopServices  = [];

        foreach ($aspects as $aspectName => $aspectConfig) {
            foreach ($container->findTaggedServiceIds($aspectName) as $serviceId => $tags) {
                foreach ($tags as $tag) {
                    if (!isset($tag['method'])) {
                        throw new \InvalidArgumentException('AOP tag must have method name supplied.');
                    }

                    // TODO: check if all options set.
                    $methodName = $tag['method'];

                    $aopServices[$serviceId][$methodName][$aspectName] = $tag;
                }
            }
        }

        foreach ($aopServices as $serviceId => $methods) {
            $this->enhanceService($container, $proxyFactory, $aspects, $serviceId, $methods);
        }
    }

    /**
     * Adds aspects to a service.
     *
     * @param ContainerBuilder $container
     * @param Reference        $proxyFactory
     * @param array            $aspectDefs
     * @param string           $serviceId
     * @param array            $methods
     */
    private function enhanceService(
        ContainerBuilder $container,
        Reference $proxyFactory,
        array $aspectDefs,
        $serviceId,
        $methods
    )
    {
        // Get service definition, clone it, and generate a new name for the clone.
        $originalDef  = $container->getDefinition($serviceId);
        $serviceDef   = $this->cloneDefinition($originalDef);
        $serviceDefId = $this->generateServiceName($container, $serviceId, 'real');

        // Compile together AOP configs for service methods.
        $methodsConfigs = [];
        foreach ($methods as $method => $aspects) {
            $methodsConfigs[$method] = [];

            foreach ($aspects as $aspectName => $tag) {
                $methodsConfigs[$method][] = [
                    'aspect'   => new Reference($aspectDefs[$aspectName]['service']),
                    'priority' => $aspectDefs[$aspectName]['priority'],
                    'config'   => $tag,
                ];

                if ($serviceDef->hasTag($aspectName)) {
                    $serviceDef->clearTag($aspectName);
                }
            }

            usort($methodsConfigs[$method], function ($aspectA, $aspectB) {
                return $aspectB['priority'] - $aspectA['priority'];
            });
        }

        // Add the cloned service to the container.
        $container->addDefinitions([
            $serviceDefId => $serviceDef,
        ]);

        // Modify the original service definition to point to proxy factory.
        $originalDef->setFactory([$proxyFactory, 'createProxy']);
        $originalDef->setArguments([
            new Reference($serviceDefId),
            $methodsConfigs,
        ]);
    }

    /**
     * Clones a service definition.
     *
     * @param Definition $sourceDef
     *
     * @return Definition
     */
    private function cloneDefinition(Definition $sourceDef)
    {
        $def = new Definition();

        $def->setFactory($sourceDef->getFactory());
        $def->setFactoryClass($sourceDef->getFactoryClass());
        $def->setFactoryMethod($sourceDef->getFactoryMethod());
        $def->setFactoryService($sourceDef->getFactoryService());
        $def->setDecoratedService($sourceDef->getDecoratedService());
        $def->setClass($sourceDef->getClass());
        $def->setArguments($sourceDef->getArguments());
        $def->setProperties($sourceDef->getProperties());
        $def->setMethodCalls($sourceDef->getMethodCalls());
        $def->setTags($def->getTags());
        $def->setFile($def->getFile());
        $def->setScope($def->getScope());
        $def->setSynchronized($def->isSynchronized());
        $def->setLazy($def->isLazy());
        $def->setSynthetic($def->isSynthetic());
        $def->setAbstract($def->isAbstract()); // @TODO: can't really work on abstract services... or can we?
        $def->setConfigurator($def->getConfigurator());
        // Don't expose the service.
        $def->setPublic(false);

        return $def;
    }

    /**
     * Generates a unique service name from name and suffix.
     *
     * @param ContainerBuilder $container
     * @param                  $serviceName
     * @param                  $suffix
     *
     * @return string
     */
    private function generateServiceName(ContainerBuilder $container, $serviceName, $suffix)
    {
        do {
            $suffix = '_' . $suffix;
            $name   = sprintf('%s.%s', $serviceName, $suffix);
            if (!$container->hasDefinition($name)) {
                return $name;
            }
        } while (true);
    }
}