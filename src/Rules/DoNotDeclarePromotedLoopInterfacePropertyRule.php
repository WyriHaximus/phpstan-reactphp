<?php

declare(strict_types=1);

namespace WyriHaximus\React\PHPStan\Rules;

use PhpParser\Node;
use PhpParser\Node\Stmt\ClassMethod;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use WyriHaximus\React\PHPStan\Config\RulesConfig;
use WyriHaximus\React\PHPStan\EventLoop\LoopInterfacePropertyViolation;

/**
 * This rule checks that no constructor promotes a parameter typed as LoopInterface to a property.
 *
 * @implements Rule<ClassMethod>
 */
final readonly class DoNotDeclarePromotedLoopInterfacePropertyRule implements Rule
{
    public function __construct(private RulesConfig $config)
    {
    }

    public function getNodeType(): string
    {
        return ClassMethod::class;
    }

    /** @inheritDoc */
    public function processNode(Node $node, Scope $scope): array
    {
        if (! $this->config->loopInterfaceProperties) {
            return [];
        }

        if ($node->name->toLowerString() === '__construct') {
            return LoopInterfacePropertyViolation::forPromotedConstructor($node, $scope);
        }

        return [];
    }
}
