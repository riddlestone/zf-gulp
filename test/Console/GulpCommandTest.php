<?php

namespace Riddlestone\ZF\Gulp\Test\Console;

use Riddlestone\ZF\Gulp\Console\GulpCommand;
use Riddlestone\ZF\Gulp\GulpFileContentGenerator;
use Exception;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class GulpCommandTest extends TestCase
{
    public function testFilePath()
    {
        $command = new GulpCommand();
        chdir('/tmp');
        $this->assertEquals('/tmp/gulpfile.js', $command->getFilePath());
        $command->setFilePath('/tmp/new-dir/gulpfile.js');
        $this->assertEquals('/tmp/new-dir/gulpfile.js', $command->getFilePath());
    }

    public function testContentGenerator()
    {
        $command = new GulpCommand();
        $generator = $this->createMock(GulpFileContentGenerator::class);
        $command->setContentGenerator($generator);
        $this->assertEquals($generator, $command->getContentGenerator());
    }

    /**
     * @throws Exception
     */
    public function testRun()
    {
        $command = new GulpCommand();
        $input = $this->createMock(InputInterface::class);
        $output = $this->createMock(OutputInterface::class);
        $generator = $this->createMock(GulpFileContentGenerator::class);
        $generator->method('getFileContents')->willReturn('SomeFileContent');
        $command->setContentGenerator($generator);
        $command->setFilePath('/tmp/gulpfile.js');
        if(is_file('/tmp/gulpfile.js')) {
            unlink('/tmp/gulpfile.js');
        }
        $this->assertFalse(is_file('/tmp/gulpfile.js'));
        $command->run($input, $output);
        $this->assertTrue(is_file('/tmp/gulpfile.js'));
        $this->assertEquals('SomeFileContent', file_get_contents('/tmp/gulpfile.js'));
        unlink('/tmp/gulpfile.js');
    }
}
