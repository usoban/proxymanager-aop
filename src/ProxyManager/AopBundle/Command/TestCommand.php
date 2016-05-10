<?php

namespace ProxyManager\AopBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Security\Core\Authentication\Token\PreAuthenticatedToken;

/**
 * @author  Urban Soban <urban.soban@dlabs.si>
 * @package ProxyManager\AopBundle\Command
 */
class TestCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('aop:test')
            ->setDescription('AOP test command.')
            ->addOption('login', null, InputOption::VALUE_NONE, 'Logins a fake user with role')
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $testService = $this->getContainer()->get('test.service');

        if ($input->getOption('login')) {
            $tokenStorage = $this->getContainer()->get('security.token_storage');
            $tokenStorage->setToken(new PreAuthenticatedToken(
                'foobar', null, 'fake', ['ROLE_ADMIN']
            ));
        }

        $testService->bar();
        $testService->foo();
        $testService->baz();
    }
}