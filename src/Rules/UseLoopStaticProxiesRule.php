<?php

declare(strict_types=1);

namespace WyriHaximus\React\PHPStan\Rules;

use PhpParser\Node;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;
use WyriHaximus\React\PHPStan\EventLoop\LoopGet;

use function array_key_exists;

/**
 * This rule checks that the static proxies on Loop are used instead of the loop instance handed out by Loop::get().
 *
 * @implements Rule<Node\Expr\MethodCall>
 */
final readonly class UseLoopStaticProxiesRule implements Rule
{
    private const array METHOD_LIST = [
        'addPeriodicTimer' => [
            'name' => 'addPeriodicTimer',
            'identifier' => 'wyrihaximus.reactphp.eventLoop.staticProxy.addPeriodicTimer',
            'message' => 'Loop::get()->addPeriodicTimer() goes through the loop instance, use React\EventLoop\Loop::addPeriodicTimer from react/event-loop instead.',
            'tip' => 'Please consult the documentation for more information: https://reactphp.org/event-loop/#addperiodictimer',
        ],
        'addReadStream' => [
            'name' => 'addReadStream',
            'identifier' => 'wyrihaximus.reactphp.eventLoop.staticProxy.addReadStream',
            'message' => 'Loop::get()->addReadStream() goes through the loop instance, use React\EventLoop\Loop::addReadStream from react/event-loop instead.',
            'tip' => 'Please consult the documentation for more information: https://reactphp.org/event-loop/#addreadstream',
        ],
        'addSignal' => [
            'name' => 'addSignal',
            'identifier' => 'wyrihaximus.reactphp.eventLoop.staticProxy.addSignal',
            'message' => 'Loop::get()->addSignal() goes through the loop instance, use React\EventLoop\Loop::addSignal from react/event-loop instead.',
            'tip' => 'Please consult the documentation for more information: https://reactphp.org/event-loop/#addsignal',
        ],
        'addTimer' => [
            'name' => 'addTimer',
            'identifier' => 'wyrihaximus.reactphp.eventLoop.staticProxy.addTimer',
            'message' => 'Loop::get()->addTimer() goes through the loop instance, use React\EventLoop\Loop::addTimer from react/event-loop instead.',
            'tip' => 'Please consult the documentation for more information: https://reactphp.org/event-loop/#addtimer',
        ],
        'addWriteStream' => [
            'name' => 'addWriteStream',
            'identifier' => 'wyrihaximus.reactphp.eventLoop.staticProxy.addWriteStream',
            'message' => 'Loop::get()->addWriteStream() goes through the loop instance, use React\EventLoop\Loop::addWriteStream from react/event-loop instead.',
            'tip' => 'Please consult the documentation for more information: https://reactphp.org/event-loop/#addwritestream',
        ],
        'cancelTimer' => [
            'name' => 'cancelTimer',
            'identifier' => 'wyrihaximus.reactphp.eventLoop.staticProxy.cancelTimer',
            'message' => 'Loop::get()->cancelTimer() goes through the loop instance, use React\EventLoop\Loop::cancelTimer from react/event-loop instead.',
            'tip' => 'Please consult the documentation for more information: https://reactphp.org/event-loop/#canceltimer',
        ],
        'futureTick' => [
            'name' => 'futureTick',
            'identifier' => 'wyrihaximus.reactphp.eventLoop.staticProxy.futureTick',
            'message' => 'Loop::get()->futureTick() goes through the loop instance, use React\EventLoop\Loop::futureTick from react/event-loop instead.',
            'tip' => 'Please consult the documentation for more information: https://reactphp.org/event-loop/#futuretick',
        ],
        'removeReadStream' => [
            'name' => 'removeReadStream',
            'identifier' => 'wyrihaximus.reactphp.eventLoop.staticProxy.removeReadStream',
            'message' => 'Loop::get()->removeReadStream() goes through the loop instance, use React\EventLoop\Loop::removeReadStream from react/event-loop instead.',
            'tip' => 'Please consult the documentation for more information: https://reactphp.org/event-loop/#removereadstream',
        ],
        'removeSignal' => [
            'name' => 'removeSignal',
            'identifier' => 'wyrihaximus.reactphp.eventLoop.staticProxy.removeSignal',
            'message' => 'Loop::get()->removeSignal() goes through the loop instance, use React\EventLoop\Loop::removeSignal from react/event-loop instead.',
            'tip' => 'Please consult the documentation for more information: https://reactphp.org/event-loop/#removesignal',
        ],
        'removeWriteStream' => [
            'name' => 'removeWriteStream',
            'identifier' => 'wyrihaximus.reactphp.eventLoop.staticProxy.removeWriteStream',
            'message' => 'Loop::get()->removeWriteStream() goes through the loop instance, use React\EventLoop\Loop::removeWriteStream from react/event-loop instead.',
            'tip' => 'Please consult the documentation for more information: https://reactphp.org/event-loop/#removewritestream',
        ],
        'run' => [
            'name' => 'run',
            'identifier' => 'wyrihaximus.reactphp.eventLoop.staticProxy.run',
            'message' => 'Loop::get()->run() goes through the loop instance, use React\EventLoop\Loop::run from react/event-loop instead.',
            'tip' => 'Please consult the documentation for more information: https://reactphp.org/event-loop/#run',
        ],
        'stop' => [
            'name' => 'stop',
            'identifier' => 'wyrihaximus.reactphp.eventLoop.staticProxy.stop',
            'message' => 'Loop::get()->stop() goes through the loop instance, use React\EventLoop\Loop::stop from react/event-loop instead.',
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
        if (! $node->name instanceof Node\Identifier) {
            return [];
        }

        $methodName = $node->name->toString();
        if (! array_key_exists($methodName, self::METHOD_LIST)) {
            return [];
        }

        if (! LoopGet::isCall($node->var)) {
            return [];
        }

        return [RuleErrorBuilder::message(self::METHOD_LIST[$methodName]['message'])->identifier(self::METHOD_LIST[$methodName]['identifier'])->tip(self::METHOD_LIST[$methodName]['tip'])->build()];
    }
}
