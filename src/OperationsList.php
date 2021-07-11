<?php

declare(strict_types=1);

namespace Sfp\Psalm\DontOperationInsideConstructor;

use function in_array;

abstract class OperationsList
{
    /** @var list<string> */
    private static array $methodLists = [
        'PDO::query',
    ];

    /** @var list<string> */
    private static array $functionsLists = [
        'opendir',
        'readdir',
        'closedir',
    ];

    public static function isOperationMethod(string $methodId) : bool
    {
        return in_array($methodId, self::$methodLists, true);
    }

    public static function isOperationFunction(string $functionId) : bool
    {
        return in_array($functionId, self::$functionsLists, true);
    }
}
