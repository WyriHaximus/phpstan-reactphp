<?php

declare(strict_types=1);

namespace WyriHaximus\React\PHPStan\EventLoop;

use PhpParser\Node;

/** This recognizes a call to the Loop::get() static proxy, the one bit of context both event loop rules need. */
final readonly class LoopGet
{
    private const string LOOP = 'react\eventloop\loop';
    private const string GET  = 'get';

    public static function isCall(Node\Expr $expr): bool
    {
        if (! ($expr instanceof Node\Expr\StaticCall)) {
            return false;
        }

        if (! ($expr->class instanceof Node\Name) || $expr->class->toLowerString() !== self::LOOP) {
            return false;
        }

        return $expr->name instanceof Node\Identifier && $expr->name->toLowerString() === self::GET;
    }
}
