<?php

declare(strict_types=1);

namespace WyriHaximus\Tests\React\PHPStan\Rules;

use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use WyriHaximus\React\PHPStan\EventLoop\LoopInterfacePropertyViolation;
use WyriHaximus\React\PHPStan\Rules\DoNotDeclareLoopInterfacePropertyRule;

use function dirname;

use const DIRECTORY_SEPARATOR;

/** @template-extends RuleTestCase<DoNotDeclareLoopInterfacePropertyRule> */
final class DoNotDeclareLoopInterfacePropertyRuleTest extends RuleTestCase
{
    /** @return list<string> */
    public static function getAdditionalConfigFiles(): array
    {
        return [dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'extension.neon'];
    }

    protected function getRule(): Rule
    {
        return new DoNotDeclareLoopInterfacePropertyRule();
    }

    public function testDeclaringLoopInterfacePropertyIsReported(): void
    {
        $this->analyse([
            dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'utils' . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'loop-edge-cases' . DIRECTORY_SEPARATOR . 'property-loop-reported.php',
        ], [
            [LoopInterfacePropertyViolation::MESSAGE, 11, LoopInterfacePropertyViolation::TIP],
            [LoopInterfacePropertyViolation::MESSAGE, 13, LoopInterfacePropertyViolation::TIP],
            [LoopInterfacePropertyViolation::MESSAGE, 13, LoopInterfacePropertyViolation::TIP],
            [LoopInterfacePropertyViolation::MESSAGE, 15, LoopInterfacePropertyViolation::TIP],
            [LoopInterfacePropertyViolation::MESSAGE, 17, LoopInterfacePropertyViolation::TIP],
            [LoopInterfacePropertyViolation::MESSAGE, 19, LoopInterfacePropertyViolation::TIP],
            [LoopInterfacePropertyViolation::MESSAGE, 22, LoopInterfacePropertyViolation::TIP],
        ]);
    }

    public function testDeclarationsThatAreNotLoopInterfaceProperties(): void
    {
        $this->analyse([
            dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'utils' . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'loop-edge-cases' . DIRECTORY_SEPARATOR . 'property-loop-not-reported.php',
        ], []);
    }
}
