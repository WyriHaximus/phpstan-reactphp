<?php

declare(strict_types=1);

namespace WyriHaximus\Tests\React\PHPStan\Rules\Config;

use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use PHPUnit\Framework\Attributes\Test;
use WyriHaximus\React\PHPStan\Config\RulesConfig;
use WyriHaximus\React\PHPStan\Rules\DoNotPassLoopInterfaceRule;

use function dirname;

use const DIRECTORY_SEPARATOR;

/** @template-extends RuleTestCase<DoNotPassLoopInterfaceRule> */
final class PassLoopInterfaceDisabledRuleTest extends RuleTestCase
{
    /** @return list<string> */
    public static function getAdditionalConfigFiles(): array
    {
        return [dirname(__DIR__, 3) . DIRECTORY_SEPARATOR . 'extension.neon'];
    }

    protected function getRule(): Rule
    {
        return new DoNotPassLoopInterfaceRule(new RulesConfig(
            blockingFunctions: true,
            loopStaticProxies: true,
            loopInstances: true,
            passLoopInterface: false,
            loopInterfaceProperties: true,
            asyncAwaitInClosure: true,
            asyncAwaitReachableViaCallStack: true,
        ));
    }

    #[Test]
    public function testNothingReported(): void
    {
        $this->analyse([
            dirname(__DIR__, 3) . DIRECTORY_SEPARATOR . 'utils' . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'loop-edge-cases' . DIRECTORY_SEPARATOR . 'pass-loop-reported.php',
        ], []);
    }
}
