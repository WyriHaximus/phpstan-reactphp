<?php

declare(strict_types=1);

namespace WyriHaximus\React\PHPStan\Rules;

use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr\CallLike;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;
use WyriHaximus\React\PHPStan\Config\RulesConfig;
use WyriHaximus\React\PHPStan\EventLoop\LoopInterfaceType;

/**
 * This rule checks that no LoopInterface instance is passed into a call.
 *
 * @implements Rule<CallLike>
 */
final readonly class DoNotPassLoopInterfaceRule implements Rule
{
    public function __construct(private RulesConfig $config)
    {
    }

    private const string IDENTIFIER = 'wyrihaximus.reactphp.eventLoop.passLoopInterface';
    private const string MESSAGE    = 'Passing a React\EventLoop\LoopInterface instance is prohibited, use the static proxies on React\EventLoop\Loop from react/event-loop instead.';
    private const string TIP        = 'Please consult the documentation for more information: https://reactphp.org/event-loop/';

    public function getNodeType(): string
    {
        return CallLike::class;
    }

    /** @inheritDoc */
    public function processNode(Node $node, Scope $scope): array
    {
        if (! $this->config->passLoopInterface) {
            return [];
        }

        $errors = [];

        foreach ($node->getRawArgs() as $arg) {
            if (! ($arg instanceof Arg) || $arg->unpack || ! LoopInterfaceType::accepts($scope->getType($arg->value))) {
                continue;
            }

            $errors[] = RuleErrorBuilder::message(self::MESSAGE)->identifier(self::IDENTIFIER)->tip(self::TIP)->build();
        }

        return $errors;
    }
}
