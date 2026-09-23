<?php

declare(strict_types=1);

namespace WyriHaximus\React\PHPStan\Rules;

use PhpParser\Node;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;
use WyriHaximus\React\PHPStan\Async\ReactAsync;
use WyriHaximus\React\PHPStan\Parser\AsyncWrappedClosureVisitor;

/**
 * This rule checks that every closure awaiting a promise is wrapped in async where it is defined.
 *
 * @implements Rule<Node\Expr\FuncCall>
 */
final readonly class WrapAwaitingClosuresInAsyncRule implements Rule
{
    public const string MESSAGE = 'await is called in a closure that isn\'t wrapped in async, wrap the closure in React\Async\async from react/async instead.';
    public const string TIP     = 'Please consult the documentation for more information: https://reactphp.org/async/#async';

    private const string IDENTIFIER = 'wyrihaximus.reactphp.async.awaitWithoutAsync';

    public function getNodeType(): string
    {
        return Node\Expr\FuncCall::class;
    }

    /** @inheritDoc */
    public function processNode(Node $node, Scope $scope): array
    {
        if (! ($node->name instanceof Node\Name\FullyQualified) || $node->name->toLowerString() !== ReactAsync::AWAIT) {
            return [];
        }

        if ($node->getAttribute(AsyncWrappedClosureVisitor::ATTRIBUTE_ENCLOSING_CLOSURE) !== false) {
            return [];
        }

        return [
            RuleErrorBuilder::message(self::MESSAGE)
                ->identifier(self::IDENTIFIER)
                ->tip(self::TIP)
                ->build(),
        ];
    }
}
