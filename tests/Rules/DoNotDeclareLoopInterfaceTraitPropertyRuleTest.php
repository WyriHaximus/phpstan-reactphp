<?php

declare(strict_types=1);

namespace WyriHaximus\Tests\React\PHPStan\Rules;

use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use WyriHaximus\React\PHPStan\EventLoop\LoopInterfacePropertyViolation;
use WyriHaximus\React\PHPStan\Rules\DoNotDeclareLoopInterfaceTraitPropertyRule;
use WyriHaximus\Tests\React\PHPStan\Support\EnabledRulesConfig;

use function dirname;

use const DIRECTORY_SEPARATOR;

/** @template-extends RuleTestCase<DoNotDeclareLoopInterfaceTraitPropertyRule> */
final class DoNotDeclareLoopInterfaceTraitPropertyRuleTest extends RuleTestCase
{
    /** @return list<string> */
    public static function getAdditionalConfigFiles(): array
    {
        return [dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'extension.neon'];
    }

    protected function getRule(): Rule
    {
        return new DoNotDeclareLoopInterfaceTraitPropertyRule(EnabledRulesConfig::get());
    }

    public function testTraitDeclaringLoopInterfacePropertyIsReported(): void
    {
        $this->analyse([
            dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'utils' . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'loop-edge-cases' . DIRECTORY_SEPARATOR . 'property-loop-trait-reported.php',
        ], [
            [LoopInterfacePropertyViolation::MESSAGE, 7, LoopInterfacePropertyViolation::TIP],
            [LoopInterfacePropertyViolation::MESSAGE, 7, LoopInterfacePropertyViolation::TIP],
        ]);
    }

    public function testTraitWithoutLoopInterfaceProperty(): void
    {
        $this->analyse([
            dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'utils' . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'loop-edge-cases' . DIRECTORY_SEPARATOR . 'property-loop-trait-not-reported.php',
        ], []);
    }
}
