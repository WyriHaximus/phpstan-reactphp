<?php

declare(strict_types=1);

namespace WyriHaximus\React\PHPStan\Collectors;

use PhpParser\Node;
use PHPStan\Analyser\Scope;
use PHPStan\Type\Type;

use function array_map;
use function sprintf;
use function strtolower;

/**
 * This resolves the keys the call graph is keyed by.
 *
 * A function like gets a key for every class in its hierarchy declaring it, so a call landing on an interface method
 * finds the implementation that awaits further down. Keys are lower cased because PHP resolves function and method
 * names case insensitively.
 */
final readonly class CallGraphKeys
{
    private const string CONSTRUCTOR = '__construct';
    private const string FUNCTION    = 'function';

    /**
     * The function like the given scope is in.
     *
     * @return array{keys: list<string>, name: string}|null
     */
    public static function current(Scope $scope): array|null
    {
        $function = $scope->getFunction();
        if ($function === null) {
            return null;
        }

        $name  = $function->getName();
        $class = $scope->getClassReflection();
        if ($class === null) {
            return ['keys' => [self::key(self::FUNCTION, $name)], 'name' => $name . '()'];
        }

        $keys = [];
        foreach ($class->getAncestors() as $ancestor) {
            if (! $ancestor->hasMethod($name)) {
                continue;
            }

            $keys[] = self::key($ancestor->getName(), $name);
        }

        return ['keys' => $keys, 'name' => $class->getName() . '::' . $name . '()'];
    }

    /**
     * The function like the given call lands on, null when nothing gets called or when the callee can't be resolved.
     *
     * @return array{keys: list<string>, name: string}|null
     */
    public static function callee(Node\Expr\CallLike $call, Scope $scope): array|null
    {
        if ($call instanceof Node\Expr\FuncCall) {
            if (! ($call->name instanceof Node\Name)) {
                return null;
            }

            $function = $scope->resolveName($call->name);

            return ['keys' => [self::key(self::FUNCTION, $function)], 'name' => $function . '()'];
        }

        if ($call instanceof Node\Expr\MethodCall) {
            return $call->name instanceof Node\Identifier
                ? self::member($scope->getType($call->var), $call->name->toString())
                : null;
        }

        if ($call instanceof Node\Expr\StaticCall) {
            return $call->name instanceof Node\Identifier && $call->class instanceof Node\Name
                ? self::member($scope->resolveTypeByName($call->class), $call->name->toString())
                : null;
        }

        if ($call instanceof Node\Expr\New_) {
            return $call->class instanceof Node\Name
                ? self::member($scope->resolveTypeByName($call->class), self::CONSTRUCTOR)
                : null;
        }

        return null;
    }

    /** @return array{keys: list<string>, name: string}|null */
    private static function member(Type $type, string $member): array|null
    {
        $classes = $type->getObjectClassNames();
        if ($classes === []) {
            return null;
        }

        return [
            'keys' => array_map(static fn (string $class): string => self::key($class, $member), $classes),
            'name' => $classes[0] . '::' . $member . '()',
        ];
    }

    private static function key(string $context, string $name): string
    {
        return strtolower(sprintf('%s::%s', $context, $name));
    }
}
