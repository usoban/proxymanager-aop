<?php

namespace ProxyManager\AopBundle\Proxy;

use Symfony\Component\HttpKernel\CacheWarmer\CacheWarmerInterface;

/**
 * TODO: warmup proxies.
 *
 * Class ProxyCacheWarmer
 *
 * @author Urban Soban <u.soban@gmail.com>
 * @package ProxyManager\AopBundle\Proxy
 */
class ProxyCacheWarmer implements CacheWarmerInterface
{
    /** @var string */
    protected $proxyCacheDir;

    /**
     * @param $proxyCacheDir
     */
    public function __construct(/*ContainerInterface $container, */$proxyCacheDir)
    {
//        $this->container     = $container;
        $this->proxyCacheDir = $proxyCacheDir;
    }

    /**
     * Checks whether this warmer is optional or not.
     *
     * Optional warmers can be ignored on certain conditions.
     *
     * A warmer should return true if the cache can be
     * generated incrementally and on-demand.
     *
     * @return bool true if the warmer is optional, false otherwise
     */
    public function isOptional()
    {
        return false;
    }

    /**
     * Will create a proxy cache directory.
     *
     * @param $cacheDir
     */
    public function warmUp($cacheDir)
    {
        // Make a proxy cache directory.
        if (!is_dir($this->proxyCacheDir)) {
            mkdir($this->proxyCacheDir, 0777); // TODO: with some more care, please... :)
        }

        // TODO: warm-up the proxies.
//        $securityCfg = $this->container->getParameter('');
    }
}