<?php

namespace Riddlestone\ZF\Gulp;

use Interop\Container\ContainerInterface;
use Interop\Container\Exception\ContainerException;
use Zend\ServiceManager\Exception\ServiceNotCreatedException;
use Zend\ServiceManager\Exception\ServiceNotFoundException;
use Zend\ServiceManager\Factory\FactoryInterface;

/**
 * Class GulpFileContentGeneratorFactory
 * @package Clockwork\Gulp
 */
class GulpFileContentGeneratorFactory implements FactoryInterface
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
        $fileWriter = new $requestedName();
        if (! $fileWriter instanceof GulpFileContentGenerator) {
            throw new ServiceNotCreatedException($requestedName . ' not an instance of ' . GulpFileContentGenerator::class);
        }
        $config = $container->get('Config');
        foreach ($config['gulp']['providers'] as $providerName) {
            $provider = $container->get($providerName);
            if (! $provider instanceof PipelineProviderInterface) {
                throw new ServiceNotCreatedException($providerName . ' not an instance of ' . PipelineProviderInterface::class);
            }
            foreach($provider->getPipelines() as $pipeline) {
                $fileWriter->addPipeline($pipeline);
            }
        }
        return $fileWriter;
    }
}
