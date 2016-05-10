<?php

namespace ProxyManager\AopBundle\Aop\Declaration\Reader;

/**
 * @author  Urban Soban <urban.soban@dlabs.si>
 * @package ProxyManager\AopBundle\Aop\Declaration\Reader
 */
class TagReader
{
    /** @var array */
    private $tagDeclaration;

    /**
     * @param $declaration
     */
    public function __construct($declaration)
    {
        if (!is_array($declaration)) {
            throw new \InvalidArgumentException('Tag declaration should be an array.');
        }

        $this->tagDeclaration = $declaration;
    }

    public function methodName()
    {

    }

    public function aspectName()
    {

    }

    public function aspectOptions()
    {

    }
}