<?php

namespace Clockwork\Gulp;

/**
 * Interface PipelineInterface
 * @package Clockwork\Gulp
 */
interface PipelineInterface
{
    public function getName(): string;

    /**
     * Each required command should be in the format "package:command", or "package" where the package is the command
     *
     * This will result in the following imports being added:
     *
     * "package:command":
     * `const { command } = require('package')`
     *
     * "package:command1", "package:command2":
     * `const { command1, command2 } = require('package')`
     *
     * "package":
     * `const package = require('package')`
     *
     * @return string[]
     */
    public function getRequiredCommands(): array;

    /**
     * The JavaScript body of the pipeline function
     *
     * @return string
     */
    public function getFunctionBody(): string;

    /**
     * Whether this pipeline should be included in the default pipeline
     *
     * @return bool
     */
    public function includeInDefault(): bool;
}
