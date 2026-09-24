<?php

declare(strict_types=1);

namespace WyriHaximus\React\PHPStan\Rules;

use PhpParser\Node;
use PhpParser\Node\Stmt\Property;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use WyriHaximus\React\PHPStan\Config\RulesConfig;
use WyriHaximus\React\PHPStan\EventLoop\LoopInterfacePropertyViolation;

/**
 * This rule checks that no class or trait declares a property typed as LoopInterface.
 *
 * @implements Rule<Property>
 */
final readonly class DoNotDeclareLoopInterfacePropertyRule implements Rule
{
    public function __construct(private RulesConfig $config)
    {
    }

    public function getNodeType(): string
    {
        return Property::class;
    }

    /** @inheritDoc */
    public function processNode(Node $node, Scope $scope): array
    {
        if (! $this->config->loopInterfaceProperties) {
            return [];
        }

        return LoopInterfacePropertyViolation::forClassProperty($node, $scope);
    }
}
