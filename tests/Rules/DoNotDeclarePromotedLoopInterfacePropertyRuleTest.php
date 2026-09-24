<?php

declare(strict_types=1);

namespace WyriHaximus\Tests\React\PHPStan\Rules;

use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use WyriHaximus\React\PHPStan\EventLoop\LoopInterfacePropertyViolation;
use WyriHaximus\React\PHPStan\Rules\DoNotDeclarePromotedLoopInterfacePropertyRule;
use WyriHaximus\Tests\React\PHPStan\Support\EnabledRulesConfig;

use function dirname;

use const DIRECTORY_SEPARATOR;

/** @template-extends RuleTestCase<DoNotDeclarePromotedLoopInterfacePropertyRule> */
final class DoNotDeclarePromotedLoopInterfacePropertyRuleTest extends RuleTestCase
{
    /** @return list<string> */
    public static function getAdditionalConfigFiles(): array
    {
        return [dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'extension.neon'];
    }

    protected function getRule(): Rule
    {
        return new DoNotDeclarePromotedLoopInterfacePropertyRule(EnabledRulesConfig::get());
    }

    public function testPromotedLoopInterfacePropertyIsReported(): void
    {
        $this->analyse([
            dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'utils' . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'loop-edge-cases' . DIRECTORY_SEPARATOR . 'property-loop-reported.php',
        ], [
            [LoopInterfacePropertyViolation::MESSAGE, 27, LoopInterfacePropertyViolation::TIP],
            [LoopInterfacePropertyViolation::MESSAGE, 27, LoopInterfacePropertyViolation::TIP],
        ]);
    }

    public function testConstructorsThatDoNotPromoteLoopInterface(): void
    {
        $this->analyse([
            dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'utils' . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'loop-edge-cases' . DIRECTORY_SEPARATOR . 'property-loop-not-reported.php',
        ], []);
    }
}
