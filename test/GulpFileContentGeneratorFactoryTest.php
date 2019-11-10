<?php

namespace Clockwork\Gulp\Test;

use Clockwork\Gulp\GulpFileContentGenerator;
use Clockwork\Gulp\GulpFileContentGeneratorFactory;
use Clockwork\Gulp\PipelineInterface;
use Clockwork\Gulp\PipelineProviderInterface;
use Exception;
use Interop\Container\ContainerInterface;
use Interop\Container\Exception\ContainerException;
use PHPUnit\Framework\TestCase;
use stdClass;
use Zend\ServiceManager\Exception\ServiceNotCreatedException;
use Zend\ServiceManager\Exception\ServiceNotFoundException;

class GulpFileContentGeneratorFactoryTest extends TestCase
{

    /**
     * @throws ContainerException
     */
    public function test__invoke()
    {
        $container = $this->createMock(ContainerInterface::class);
        $container->method('get')
            ->willReturnCallback(function ($name) {
                switch ($name) {
                    case 'Config':
                        return [
                            'gulp' => [
                                'providers' => [
                                    'TestProvider'
                                ],
                            ],
                        ];
                    case 'TestProvider':
                        $provider = $this->createMock(PipelineProviderInterface::class);
                        $provider->method('getPipelines')
                            ->willReturn([$this->createMock(PipelineInterface::class)]);
                        return $provider;
                    default:
                        throw new ServiceNotFoundException();
                }
            });
        $factory = new GulpFileContentGeneratorFactory();
        $generator = $factory($container, GulpFileContentGenerator::class);
        $this->assertInstanceOf(GulpFileContentGenerator::class, $generator);
        $this->assertCount(1, $generator->getPipelines());

        try {
            $factory($container, stdClass::class);
            $this->fail('Exception not thrown');
        } catch (Exception $e) {
            $this->assertInstanceOf(ServiceNotCreatedException::class, $e);
        }

        $container = $this->createMock(ContainerInterface::class);
        $container->method('get')
            ->willReturnCallback(function ($name) {
                switch ($name) {
                    case 'Config':
                        return [
                            'gulp' => [
                                'providers' => [
                                    'TestProvider'
                                ],
                            ],
                        ];
                    case 'TestProvider':
                        return new stdClass();
                    default:
                        throw new ServiceNotFoundException();
                }
            });
        try {
            $factory($container, GulpFileContentGenerator::class);
            $this->fail('Exception not thrown');
        } catch (Exception $e) {
            $this->assertInstanceOf(ServiceNotCreatedException::class, $e);
        }
    }
}
