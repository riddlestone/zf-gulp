<?php

namespace Riddlestone\ZF\Gulp\Console;

use Riddlestone\ZF\Gulp\GulpFileContentGenerator;
use Interop\Container\ContainerInterface;
use Interop\Container\Exception\ContainerException;
use Zend\ServiceManager\Exception\ServiceNotCreatedException;
use Zend\ServiceManager\Exception\ServiceNotFoundException;
use Zend\ServiceManager\Factory\FactoryInterface;

/**
 * Class GulpCommandFactory
 * @package Clockwork\Gulp
 */
class GulpCommandFactory implements FactoryInterface
{
    /**
     * Create an object
     *
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param null|array $options
     * @return object
     * @throws ServiceNotFoundException if unable to resolve the service.
     * @throws ServiceNotCreatedException if an exception is raised when
     *     creating a service.
     * @throws ContainerException if any other error occurs
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $command = new $requestedName();
        if (! $command instanceof GulpCommand) {
            throw new ServiceNotCreatedException($requestedName . ' not an instance of ' . GulpCommand::class);
        }
        $command->setContentGenerator($container->get(GulpFileContentGenerator::class));
        $command->setFilePath(defined('APPLICATION_PATH') ? APPLICATION_PATH : getcwd());
        return $command;
    }
}
