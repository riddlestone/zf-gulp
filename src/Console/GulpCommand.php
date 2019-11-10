<?php

namespace Riddlestone\ZF\Gulp\Console;

use Riddlestone\ZF\Gulp\GulpFileContentGenerator;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class GulpCommand extends Command
{
    /**
     * @var string|null The default command name
     */
    protected static $defaultName = 'gulp';

    /**
     * @var GulpFileContentGenerator
     */
    protected $contentGenerator;

    /**
     * @var string
     */
    protected $filePath;

    /**
     * @param GulpFileContentGenerator $contentGenerator
     */
    public function setContentGenerator(GulpFileContentGenerator $contentGenerator): void
    {
        $this->contentGenerator = $contentGenerator;
    }

    /**
     * @return GulpFileContentGenerator
     */
    public function getContentGenerator(): GulpFileContentGenerator
    {
        return $this->contentGenerator;
    }

    /**
     * @param string $filePath
     */
    public function setFilePath(string $filePath): void
    {
        $this->filePath = $filePath;
    }

    /**
     * @return string
     */
    public function getFilePath(): string
    {
        return $this->filePath ?: getcwd() . '/gulpfile.js';
    }

    /**
     * Build the gulp configuration file
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null null or 0 if everything went fine, or an error code
     * @throws \Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $result = file_put_contents($this->getFilePath(), $this->getContentGenerator()->getFileContents());
        return $result === false ? 1 : 0;
    }
}
