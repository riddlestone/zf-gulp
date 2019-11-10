<?php

namespace Clockwork\Gulp\Exception;

use Exception;
use Throwable;

class PackageAndPackagePropertyImportedException extends Exception
{
    public function __construct($message = 'Cannot have a package and a package property both as imports', $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
