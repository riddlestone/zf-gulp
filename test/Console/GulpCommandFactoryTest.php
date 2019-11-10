<?php

namespace Riddlestone\ZF\Gulp\Test\Console;

use Riddlestone\ZF\Gulp\Console\GulpCommand;
use Riddlestone\ZF\Gulp\Console\GulpCommandFactory;
use Riddlestone\ZF\Gulp\GulpFileContentGenerator;
use Exception;
use Interop\Container\ContainerInterface;
use Interop\Container\Exception\ContainerException;
use PHPUnit\Framework\TestCase;
use stdClass;
use Zend\ServiceManager\Exception\ServiceNotCreatedException;
use Zend\ServiceManager\Exception\ServiceNotFoundException;

/**
 * Class GulpCommandFactoryTest
 * @package Clockwork\Gulp\Test
 */
class GulpCommandFactoryTest extends TestCase
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
                    case GulpFileContentGenerator::class:
                        return $this->createMock($name);
                    default:
                        throw new ServiceNotFoundException();
                }
            });
        $factory = new GulpCommandFactory();
        $command = $factory($container, GulpCommand::class);
        $this->assertInstanceOf(GulpCommand::class, $command);

        try {
            $factory($container, stdClass::class);
            $this->fail('Exception not thrown');
        } catch(Exception $e) {
            $this->assertInstanceOf(ServiceNotCreatedException::class, $e);
        }
    }
}
