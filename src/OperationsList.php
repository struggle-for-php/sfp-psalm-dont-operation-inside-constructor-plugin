<?php

declare(strict_types=1);

namespace Sfp\Psalm\DontOperationInsideConstructor;

use Sfp\ResourceOperations\ResourceOperations;
use function in_array;
use function array_merge;

abstract class OperationsList
{
    /** @var list<string> */
    private static array $methodLists = [
        'PDO::query',
    ];

    /** @var list<string> */
    private static array $functionsLists = [
    ];

    public static function isOperationMethod(string $methodId) : bool
    {
        return in_array($methodId, array_merge(self::$methodLists, ResourceOperations::getMethods()), true);
    }

    public static function isOperationFunction(string $functionId) : bool
    {
        return in_array($functionId, array_merge(self::$functionsLists, ResourceOperations::getFunctions()), true);
    }
}
