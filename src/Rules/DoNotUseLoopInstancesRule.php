<?php

declare(strict_types=1);

namespace WyriHaximus\React\PHPStan\Rules;

use PhpParser\Node;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;
use WyriHaximus\React\PHPStan\Config\RulesConfig;
use WyriHaximus\React\PHPStan\EventLoop\LoopGet;
use WyriHaximus\React\PHPStan\EventLoop\LoopInterfaceType;

use function array_key_exists;

/**
 * This rule checks that no loop instance is used, the static proxies on Loop are to be used instead.
 *
 * Calls on Loop::get() are left to UseLoopStaticProxiesRule so a single call never gets reported twice.
 *
 * @implements Rule<Node\Expr\MethodCall>
 */
final readonly class DoNotUseLoopInstancesRule implements Rule
{
    public function __construct(private RulesConfig $config)
    {
    }

    private const array METHOD_LIST = [
        'addPeriodicTimer' => [
            'name' => 'addPeriodicTimer',
            'identifier' => 'wyrihaximus.reactphp.eventLoop.instance.addPeriodicTimer',
            'message' => 'Calling addPeriodicTimer on a loop instance is prohibited, use React\EventLoop\Loop::addPeriodicTimer from react/event-loop instead.',
            'tip' => 'Please consult the documentation for more information: https://reactphp.org/event-loop/#addperiodictimer',
        ],
        'addReadStream' => [
            'name' => 'addReadStream',
            'identifier' => 'wyrihaximus.reactphp.eventLoop.instance.addReadStream',
            'message' => 'Calling addReadStream on a loop instance is prohibited, use React\EventLoop\Loop::addReadStream from react/event-loop instead.',
            'tip' => 'Please consult the documentation for more information: https://reactphp.org/event-loop/#addreadstream',
        ],
        'addSignal' => [
            'name' => 'addSignal',
            'identifier' => 'wyrihaximus.reactphp.eventLoop.instance.addSignal',
            'message' => 'Calling addSignal on a loop instance is prohibited, use React\EventLoop\Loop::addSignal from react/event-loop instead.',
            'tip' => 'Please consult the documentation for more information: https://reactphp.org/event-loop/#addsignal',
        ],
        'addTimer' => [
            'name' => 'addTimer',
            'identifier' => 'wyrihaximus.reactphp.eventLoop.instance.addTimer',
            'message' => 'Calling addTimer on a loop instance is prohibited, use React\EventLoop\Loop::addTimer from react/event-loop instead.',
            'tip' => 'Please consult the documentation for more information: https://reactphp.org/event-loop/#addtimer',
        ],
        'addWriteStream' => [
            'name' => 'addWriteStream',
            'identifier' => 'wyrihaximus.reactphp.eventLoop.instance.addWriteStream',
            'message' => 'Calling addWriteStream on a loop instance is prohibited, use React\EventLoop\Loop::addWriteStream from react/event-loop instead.',
            'tip' => 'Please consult the documentation for more information: https://reactphp.org/event-loop/#addwritestream',
        ],
        'cancelTimer' => [
            'name' => 'cancelTimer',
            'identifier' => 'wyrihaximus.reactphp.eventLoop.instance.cancelTimer',
            'message' => 'Calling cancelTimer on a loop instance is prohibited, use React\EventLoop\Loop::cancelTimer from react/event-loop instead.',
            'tip' => 'Please consult the documentation for more information: https://reactphp.org/event-loop/#canceltimer',
        ],
        'futureTick' => [
            'name' => 'futureTick',
            'identifier' => 'wyrihaximus.reactphp.eventLoop.instance.futureTick',
            'message' => 'Calling futureTick on a loop instance is prohibited, use React\EventLoop\Loop::futureTick from react/event-loop instead.',
            'tip' => 'Please consult the documentation for more information: https://reactphp.org/event-loop/#futuretick',
        ],
        'removeReadStream' => [
            'name' => 'removeReadStream',
            'identifier' => 'wyrihaximus.reactphp.eventLoop.instance.removeReadStream',
            'message' => 'Calling removeReadStream on a loop instance is prohibited, use React\EventLoop\Loop::removeReadStream from react/event-loop instead.',
            'tip' => 'Please consult the documentation for more information: https://reactphp.org/event-loop/#removereadstream',
        ],
        'removeSignal' => [
            'name' => 'removeSignal',
            'identifier' => 'wyrihaximus.reactphp.eventLoop.instance.removeSignal',
            'message' => 'Calling removeSignal on a loop instance is prohibited, use React\EventLoop\Loop::removeSignal from react/event-loop instead.',
            'tip' => 'Please consult the documentation for more information: https://reactphp.org/event-loop/#removesignal',
        ],
        'removeWriteStream' => [
            'name' => 'removeWriteStream',
            'identifier' => 'wyrihaximus.reactphp.eventLoop.instance.removeWriteStream',
            'message' => 'Calling removeWriteStream on a loop instance is prohibited, use React\EventLoop\Loop::removeWriteStream from react/event-loop instead.',
            'tip' => 'Please consult the documentation for more information: https://reactphp.org/event-loop/#removewritestream',
        ],
        'run' => [
            'name' => 'run',
            'identifier' => 'wyrihaximus.reactphp.eventLoop.instance.run',
            'message' => 'Calling run on a loop instance is prohibited, use React\EventLoop\Loop::run from react/event-loop instead.',
            'tip' => 'Please consult the documentation for more information: https://reactphp.org/event-loop/#run',
        ],
        'stop' => [
            'name' => 'stop',
            'identifier' => 'wyrihaximus.reactphp.eventLoop.instance.stop',
            'message' => 'Calling stop on a loop instance is prohibited, use React\EventLoop\Loop::stop from react/event-loop instead.',
            'tip' => 'Please consult the documentation for more information: https://reactphp.org/event-loop/#stop',
        ],
    ];

    public function getNodeType(): string
    {
        return Node\Expr\MethodCall::class;
    }

    /** @inheritDoc */
    public function processNode(Node $node, Scope $scope): array
    {
        if (! $this->config->loopInstances) {
            return [];
        }

        if (! $node->name instanceof Node\Identifier) {
            return [];
        }

        $methodName = $node->name->toString();
        if (! array_key_exists($methodName, self::METHOD_LIST)) {
            return [];
        }

        if (LoopGet::isCall($node->var)) {
            return [];
        }

        if (! LoopInterfaceType::accepts($scope->getType($node->var))) {
            return [];
        }

        return [RuleErrorBuilder::message(self::METHOD_LIST[$methodName]['message'])->identifier(self::METHOD_LIST[$methodName]['identifier'])->tip(self::METHOD_LIST[$methodName]['tip'])->build()];
    }
}
