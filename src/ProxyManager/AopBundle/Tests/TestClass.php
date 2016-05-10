<?php

namespace ProxyManager\AopBundle\Tests;

/**
 * @author  Urban Soban <urban.soban@dlabs.si>
 * @package ProxyManager\AopBundle\Tests
 */
class TestClass
{
    public function foo()
    {
        var_dump('Aspects applied :)');
    }

    public function bar()
    {
        var_dump('No aspect applied.');
    }

    public function baz()
    {
        var_dump('Some aspects applied.');
    }
}