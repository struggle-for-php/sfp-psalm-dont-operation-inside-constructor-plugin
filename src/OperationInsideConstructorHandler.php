<?php

declare(strict_types=1);

namespace Sfp\Psalm\DontOperationInsideConstructor;

use Psalm\CodeLocation;
use Psalm\IssueBuffer;
use Psalm\Plugin\EventHandler\AfterEveryFunctionCallAnalysisInterface;
use Psalm\Plugin\EventHandler\AfterMethodCallAnalysisInterface;
use Psalm\Plugin\EventHandler\Event\AfterEveryFunctionCallAnalysisEvent;
use Psalm\Plugin\EventHandler\Event\AfterMethodCallAnalysisEvent;
use Sfp\Psalm\DontOperationInsideConstructor\Issue\FunctionOperationInsideConstructorIssue;
use Sfp\Psalm\DontOperationInsideConstructor\Issue\MethodOperationInsideConstructorIssue;
use function explode;
use function sprintf;

final class OperationInsideConstructorHandler implements AfterMethodCallAnalysisInterface, AfterEveryFunctionCallAnalysisInterface
{
    public static function afterMethodCallAnalysis(AfterMethodCallAnalysisEvent $event): void
    {
        $calling_method_id  = $event->getContext()->calling_method_id;
        if ($calling_method_id === null) {
            return ;
        }

        if ($event->getContext()->collect_initializations === true) {
            return;
        }

        [, $method] = explode('::', $calling_method_id);
        if ($method !== '__construct') {
            return;
        }

        if (! OperationsList::isOperationMethod($event->getDeclaringMethodId())) {
            return ;
        }

        $codeLocation = new CodeLocation($event->getStatementsSource(), $event->getExpr());
        IssueBuffer::accepts(new MethodOperationInsideConstructorIssue(sprintf("'%s' not allowed inside __construct()", $event->getDeclaringMethodId()), $codeLocation));
    }

    public static function afterEveryFunctionCallAnalysis(
        AfterEveryFunctionCallAnalysisEvent $event
    ): void {

        $calling_method_id  = $event->getContext()->calling_method_id;
        if ($calling_method_id === null) {
            return ;
        }
        [, $method] = explode('::', $calling_method_id);

        if ($method !== '__construct') {
            return ;
        }

        if (! OperationsList::isOperationFunction($event->getFunctionId())) {
            return ;
        }

        $codeLocation = new CodeLocation($event->getStatementsSource(), $event->getExpr());
        IssueBuffer::accepts(new FunctionOperationInsideConstructorIssue(sprintf("'%s' not allowed inside __construct()", $event->getFunctionId()), $codeLocation));
    }
}
