<?php

declare(strict_types=1);

namespace WyriHaximus\React\PHPStan\Parser;

use PhpParser\Node;
use PhpParser\NodeVisitor;
use WyriHaximus\React\PHPStan\Async\ReactAsync;
use WyriHaximus\React\PHPStan\Config\RulesConfig;

use function array_pop;
use function count;

/**
 * This visitor records, for every function call, whether the closure directly around it is wrapped in async.
 *
 * Rules only get handed the node they asked for and have no way to walk up to its parents, so the surrounding context
 * has to be recorded while the file is parsed.
 */
final class AsyncWrappedClosureVisitor implements NodeVisitor
{
    /**
     * Holds the state of the closure directly surrounding a call: null when there is no surrounding closure, true when
     * that closure is wrapped in async, and false when it isn't.
     */
    public const string ATTRIBUTE_ENCLOSING_CLOSURE = 'wyrihaximus.reactphp.async.enclosingClosure';

    private const string ATTRIBUTE_WRAPPED_IN_ASYNC = 'wyrihaximus.reactphp.async.wrappedInAsync';
    /**
     * The function likes we're currently inside of, innermost last, holding false for closures and null for named
     * functions and methods as those never run inside the fiber of the closure they are declared in.
     *
     * @var list<bool|null>
     */
    private array $functionLikes = [];

    public function __construct(private readonly RulesConfig $config)
    {
    }

    /** @inheritDoc */
    public function beforeTraverse(array $nodes): null
    {
        $this->functionLikes = [];

        return null;
    }

    public function enterNode(Node $node): null
    {
        if (! $this->config->asyncParserEnabled) {
            return null;
        }

        if ($node instanceof Node\Expr\FuncCall) {
            $this->markClosuresWrappedInAsync($node);
        }

        if ($node instanceof Node\Expr\CallLike) {
            $node->setAttribute(self::ATTRIBUTE_ENCLOSING_CLOSURE, $this->enclosingClosure());
        }

        if ($node instanceof Node\FunctionLike) {
            $this->functionLikes[] = $node instanceof Node\Expr\Closure || $node instanceof Node\Expr\ArrowFunction
                ? $node->getAttribute(self::ATTRIBUTE_WRAPPED_IN_ASYNC, false) === true
                : null;
        }

        return null;
    }

    public function leaveNode(Node $node): null
    {
        if ($node instanceof Node\FunctionLike) {
            array_pop($this->functionLikes);
        }

        return null;
    }

    /** @inheritDoc */
    public function afterTraverse(array $nodes): null
    {
        return null;
    }

    private function markClosuresWrappedInAsync(Node\Expr\FuncCall $node): void
    {
        if (! ($node->name instanceof Node\Name\FullyQualified) || $node->name->toLowerString() !== ReactAsync::ASYNC) {
            return;
        }

        if ($node->isFirstClassCallable()) {
            return;
        }

        foreach ($node->getArgs() as $arg) {
            if (! ($arg->value instanceof Node\Expr\Closure) && ! ($arg->value instanceof Node\Expr\ArrowFunction)) {
                continue;
            }

            $arg->value->setAttribute(self::ATTRIBUTE_WRAPPED_IN_ASYNC, true);
        }
    }

    private function enclosingClosure(): bool|null
    {
        $functionLikeCount = count($this->functionLikes);
        if ($functionLikeCount === 0) {
            return null;
        }

        return $this->functionLikes[$functionLikeCount - 1];
    }
}
