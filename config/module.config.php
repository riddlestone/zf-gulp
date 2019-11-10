<?php

namespace Riddlestone\ZF\Gulp;

return [
    'console' => [
        'commands' => [
            Console\GulpCommand::class,
        ],
    ],
    'gulp' => [
        'providers' => [],
    ],
    'service_manager' => [
        'factories' => [
            Console\GulpCommand::class => Console\GulpCommandFactory::class,
            GulpFileContentGenerator::class => GulpFileContentGeneratorFactory::class,
        ],
    ],
];
