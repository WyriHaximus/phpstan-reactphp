<?php

declare(strict_types=1);

namespace WyriHaximus\React\PHPStan\EventLoop;

use PhpParser\Node;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Name;
use PhpParser\Node\NullableType;
use PhpParser\Node\Param;
use PhpParser\Node\PropertyItem;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Property;
use PhpParser\Node\Stmt\Trait_;
use PhpParser\Node\UnionType;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\IdentifierRuleError;
use PHPStan\Rules\RuleErrorBuilder;

use function array_filter;
use function array_map;
use function array_push;
use function array_values;
use function is_string;

final readonly class LoopInterfacePropertyViolation
{
    public const string IDENTIFIER = 'wyrihaximus.reactphp.eventLoop.propertyLoopInterface';
    public const string MESSAGE    = 'Declaring a React\EventLoop\LoopInterface property is prohibited, use the static proxies on React\EventLoop\Loop from react/event-loop instead.';
    public const string TIP        = 'Please consult the documentation for more information: https://reactphp.org/event-loop/';

    public static function declaresLoopInterface(string $name, Scope $scope, Node $nativeTypeHint): bool
    {
        if (self::nativeTypeHintDeclaresLoopInterface($nativeTypeHint)) {
            return true;
        }

        $property = $scope->getClassReflection()?->getNativeProperty($name);

        return $property !== null && LoopInterfaceType::accepts($property->getReadableType());
    }

    public static function declaresLoopInterfaceFromReflection(string $name, Scope $scope): bool
    {
        $property = $scope->getClassReflection()?->getNativeProperty($name);

        return $property !== null && LoopInterfaceType::accepts($property->getReadableType());
    }

    public static function create(): IdentifierRuleError
    {
        return RuleErrorBuilder::message(self::MESSAGE)->identifier(self::IDENTIFIER)->tip(self::TIP)->build();
    }

    /** @return list<IdentifierRuleError> */
    public static function forClassProperty(Property $node, Scope $scope): array
    {
        $violating = array_filter(
            $node->props,
            static fn (PropertyItem $property): bool => self::shouldReportClassProperty($property->name->toString(), $node, $scope),
        );

        return array_values(array_map(static fn (): IdentifierRuleError => self::create(), $violating));
    }

    /** @return list<IdentifierRuleError> */
    public static function forTrait(Trait_ $node, Scope $scope): array
    {
        $errors = [];

        foreach ($node->stmts as $stmt) {
            if (! ($stmt instanceof Property) || $stmt->type === null) {
                continue;
            }

            $violating = array_filter(
                $stmt->props,
                static fn (PropertyItem $property): bool => self::declaresLoopInterface($property->name->toString(), $scope, $stmt->type),
            );

            array_push($errors, ...array_map(static fn (): IdentifierRuleError => self::create(), $violating));
        }

        return $errors;
    }

    public static function shouldReportClassProperty(string $name, Property $node, Scope $scope): bool
    {
        $type = $node->type;

        return ($type !== null && self::declaresLoopInterface($name, $scope, $type))
            || self::declaresLoopInterfaceFromReflection($name, $scope);
    }

    /** @return list<IdentifierRuleError> */
    public static function forPromotedConstructor(ClassMethod $node, Scope $scope): array
    {
        $violating = array_filter(
            $node->params,
            static fn (Param $param): bool => $param->flags !== 0
                && $param->var instanceof Variable
                && is_string($param->var->name)
                && $param->type !== null
                && self::declaresLoopInterface($param->var->name, $scope, $param->type),
        );

        return array_values(array_map(static fn (): IdentifierRuleError => self::create(), $violating));
    }

    private static function nativeTypeHintDeclaresLoopInterface(Node $typeNode): bool
    {
        if ($typeNode instanceof NullableType) {
            return self::nativeTypeHintDeclaresLoopInterface($typeNode->type);
        }

        if ($typeNode instanceof UnionType) {
            foreach ($typeNode->types as $type) {
                if (self::nativeTypeHintDeclaresLoopInterface($type)) {
                    return true;
                }
            }

            return false;
        }

        if ($typeNode instanceof Name) {
            $lower = $typeNode->toLowerString();

            return $lower === 'react\eventloop\loopinterface' || $lower === 'loopinterface';
        }

        return false;
    }
}
