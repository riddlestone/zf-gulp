<?php

namespace Clockwork\Gulp\Test;

use Clockwork\Gulp\Console\GulpCommand;
use Clockwork\Gulp\GulpFileContentGenerator;
use Clockwork\Gulp\Module;
use PHPUnit\Framework\TestCase;

class ModuleTest extends TestCase
{

    public function testGetConfig()
    {
        $module = new Module();
        $config = $module->getConfig();
        $this->assertIsArray($config);

        $this->assertIsArray($config['console']);
        $this->assertIsArray($config['console']['commands']);
        $this->assertContains(GulpCommand::class, $config['console']['commands']);

        $this->assertIsArray($config['gulp']);
        $this->assertIsArray($config['gulp']['providers']);

        $this->assertIsArray($config['service_manager']);
        $this->assertIsArray($config['service_manager']['factories']);
        $this->assertArrayHasKey(GulpCommand::class, $config['service_manager']['factories']);
        $this->assertArrayHasKey(GulpFileContentGenerator::class, $config['service_manager']['factories']);
    }
}
