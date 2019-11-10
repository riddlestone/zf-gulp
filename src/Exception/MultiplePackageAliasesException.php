<?php

namespace Riddlestone\ZF\Gulp\Exception;

use Exception;
use Throwable;

class MultiplePackageAliasesException extends Exception
{
    public function __construct($message = 'Cannot have two aliases for a single package', $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
