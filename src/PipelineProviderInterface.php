<?php

namespace Riddlestone\ZF\Gulp;

/**
 * Interface PipelineProviderInterface
 * @package Clockwork\Gulp
 */
interface PipelineProviderInterface
{
    /**
     * Returns the Gulp Pipelines
     *
     * @return PipelineInterface[]
     */
    public function getPipelines(): array;
}
