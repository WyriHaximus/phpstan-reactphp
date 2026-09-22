<?php

declare(strict_types=1);

namespace WyriHaximus\React\PHPStan\Rules;

use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr\CallLike;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;
use PHPStan\Type\ObjectType;

/**
 * This rule checks that no LoopInterface instance is passed into a call.
 *
 * @implements Rule<CallLike>
 */
final readonly class DoNotPassLoopInterfaceRule implements Rule
{
    private const string LOOP_INTERFACE = 'React\EventLoop\LoopInterface';
    private const string IDENTIFIER     = 'wyrihaximus.reactphp.eventLoop.passLoopInterface';
    private const string MESSAGE        = 'Passing a React\EventLoop\LoopInterface instance is prohibited, use the static proxies on React\EventLoop\Loop from react/event-loop instead.';
    private const string TIP            = 'Please consult the documentation for more information: https://reactphp.org/event-loop/';

    public function getNodeType(): string
    {
        return CallLike::class;
    }

    /** @inheritDoc */
    public function processNode(Node $node, Scope $scope): array
    {
        $loopType = new ObjectType(self::LOOP_INTERFACE);
        $errors   = [];

        foreach ($node->getRawArgs() as $arg) {
            if (! ($arg instanceof Arg) || $arg->unpack || ! $loopType->isSuperTypeOf($scope->getType($arg->value))->yes()) {
                continue;
            }

            $errors[] = RuleErrorBuilder::message(self::MESSAGE)->identifier(self::IDENTIFIER)->tip(self::TIP)->build();
        }

        return $errors;
    }
}
