<?php

declare(strict_types=1);

namespace WyriHaximus\React\PHPStan\Collectors;

use PhpParser\Node;
use PHPStan\Analyser\Scope;
use PHPStan\Collectors\Collector;
use WyriHaximus\React\PHPStan\Async\ReactAsync;
use WyriHaximus\React\PHPStan\Parser\AsyncWrappedClosureVisitor;

/**
 * This records awaits, call-graph edges, and roots from non-async closures for WrapClosuresReachingAwaitInAsyncRule.
 *
 * @phpstan-type AsyncCallGraphAwait array{type: 'await', keys: list<string>, name: string, file: string, line: int}
 * @phpstan-type AsyncCallGraphRoot array{type: 'root', callees: list<string>, name: string, file: string, line: int}
 * @phpstan-type AsyncCallGraphEdge array{type: 'edge', callers: list<string>, callees: list<string>, file: string, line: int}
 * @phpstan-type AsyncCallGraphRecord AsyncCallGraphAwait|AsyncCallGraphRoot|AsyncCallGraphEdge
 * @implements Collector<Node\Expr\CallLike, AsyncCallGraphRecord>
 */
final readonly class AsyncCallGraphCollector implements Collector
{
    public function getNodeType(): string
    {
        return Node\Expr\CallLike::class;
    }

    public function processNode(Node $node, Scope $scope): array|null
    {
        if (
            $node instanceof Node\Expr\FuncCall
            && $node->name instanceof Node\Name\FullyQualified
            && $node->name->toLowerString() === ReactAsync::AWAIT
            && $node->getAttribute(AsyncWrappedClosureVisitor::ATTRIBUTE_ENCLOSING_CLOSURE) === null
        ) {
            $current = CallGraphKeys::current($scope);
            if ($current === null) {
                return null;
            }

            return [
                'type' => 'await',
                'keys' => $current['keys'],
                'name' => $current['name'],
                'file' => $scope->getFile(),
                'line' => $node->getStartLine(),
            ];
        }

        $callee = CallGraphKeys::callee($node, $scope);
        if ($callee === null) {
            return null;
        }

        $enclosingClosure = $node->getAttribute(AsyncWrappedClosureVisitor::ATTRIBUTE_ENCLOSING_CLOSURE);
        if ($enclosingClosure === true) {
            return null;
        }

        if ($enclosingClosure === false) {
            return [
                'type' => 'root',
                'callees' => $callee['keys'],
                'name' => $callee['name'],
                'file' => $scope->getFile(),
                'line' => $node->getStartLine(),
            ];
        }

        $current = CallGraphKeys::current($scope);
        if ($current === null) {
            return null;
        }

        return [
            'type' => 'edge',
            'callers' => $current['keys'],
            'callees' => $callee['keys'],
            'file' => $scope->getFile(),
            'line' => $node->getStartLine(),
        ];
    }
}
