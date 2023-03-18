<?php

declare(strict_types=1);

namespace Sfp\Psalm\DontOperationInsideConstructor;

use Psalm\Plugin\PluginEntryPointInterface;
use Psalm\Plugin\RegistrationInterface;
use SimpleXMLElement;

/** @psalm-suppress UnusedClass */
class Plugin implements PluginEntryPointInterface
{
    public function __invoke(RegistrationInterface $registration, ?SimpleXMLElement $config = null): void
    {
        require_once __DIR__ . '/OperationInsideConstructorHandler.php';
        $registration->registerHooksFromClass(OperationInsideConstructorHandler::class);
    }
}
