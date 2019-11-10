<?php

namespace Clockwork\Gulp;

use Clockwork\Gulp\Exception\MultiplePackageAliasesException;
use Clockwork\Gulp\Exception\PackageAndPackagePropertyImportedException;
use Exception;

/**
 * Class GulpFileContentGenerator
 * @package Clockwork\Gulp
 */
class GulpFileContentGenerator
{
    /**
     * @var PipelineInterface[]
     */
    protected $pipelines = [];

    public function addPipeline(PipelineInterface $pipeline)
    {
        $this->pipelines[$pipeline->getName()] = $pipeline;
    }

    /**
     * @return PipelineInterface[]
     */
    public function getPipelines()
    {
        return $this->pipelines;
    }

    /**
     * @return string
     * @throws Exception
     */
    public function getFileContents()
    {
        return $this->renderImports() . "\n\n" . $this->renderFunctions() . "\n\n" . $this->renderExports() . "\n";
    }

    /**
     * @return string
     * @throws Exception
     */
    public function renderImports()
    {
        $output = [];
        $packageCommands = [
            'gulp' => [
                'parallel',
            ],
        ];
        foreach ($this->pipelines as $pipeline) {
            foreach ($pipeline->getRequiredCommands() as $command) {
                $commandParts = explode(':', $command);
                if (count($commandParts) == 2) {
                    // command is package:command
                    if (isset($packageCommands[$commandParts[0]])) {
                        if (! is_array($packageCommands[$commandParts[0]])) {
                            throw new PackageAndPackagePropertyImportedException();
                        }
                        if (! in_array($commandParts[1], $packageCommands[$commandParts[0]])) {
                            $packageCommands[$commandParts[0]][] = $commandParts[1];
                        }
                    } else {
                        $packageCommands[$commandParts[0]] = [$commandParts[1]];
                    }
                    continue;
                }
                $commandParts = explode('|', $command);
                if (count($commandParts) == 1) {
                    $commandParts[1] = $commandParts[0];
                }
                // command is package|alias
                if (isset($packageCommands[$commandParts[0]])) {
                    if (is_array($packageCommands[$commandParts[0]])) {
                        throw new PackageAndPackagePropertyImportedException();
                    } elseif ($packageCommands[$commandParts[0]] != $commandParts[1]) {
                        throw new MultiplePackageAliasesException();
                    }
                } else {
                    $packageCommands[$commandParts[0]] = $commandParts[1];
                }
            }
        }
        ksort($packageCommands);
        foreach ($packageCommands as $package => $commands) {
            if (is_array($commands)) {
                sort($commands);
            }
            $output[] = sprintf('%s = require(\'%s\')', is_array($commands) ? '{ ' . implode(', ', $commands) . ' }' : $commands, $package);
        }
        return $output ? 'const ' . implode(",\n    ", $output) . ';' : '';
    }

    public function renderFunctions()
    {
        $output = [];
        foreach ($this->pipelines as $pipeline) {
            $output[] = sprintf('function %s() {%s}', $pipeline->getName(), "\n    " . str_replace("\n", "\n    ", $pipeline->getFunctionBody()) . "\n");
        }
        return implode("\n\n", $output);
    }

    public function renderExports()
    {
        $output = [];
        foreach ($this->getPipelines() as $pipeline) {
            $output[] = sprintf('exports.%1$s = %1$s;', $pipeline->getName());
        }
        if($inDefault = array_filter($this->getPipelines(), function(PipelineInterface $pipeline){
            return $pipeline->includeInDefault();
        })) {
            $output[] = sprintf('exports.default = parallel(%s);', implode(', ', array_map(function (PipelineInterface $pipeline) {
                return $pipeline->getName();
            }, $inDefault)));
        }
        return implode("\n", $output);
    }
}
