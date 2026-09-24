<?php

declare(strict_types=1);

namespace WyriHaximus\Tests\React\PHPStan\Rules\Config;

use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use PHPUnit\Framework\Attributes\Test;
use WyriHaximus\React\PHPStan\Config\RulesConfig;
use WyriHaximus\React\PHPStan\Rules\UseLoopStaticProxiesRule;
use WyriHaximus\React\PHPStan\Utils\ListLoopMethods;

use function dirname;

use const DIRECTORY_SEPARATOR;

/** @template-extends RuleTestCase<UseLoopStaticProxiesRule> */
final class LoopStaticProxiesDisabledRuleTest extends RuleTestCase
{
    /** @return list<string> */
    public static function getAdditionalConfigFiles(): array
    {
        return [dirname(__DIR__, 3) . DIRECTORY_SEPARATOR . 'extension.neon'];
    }

    protected function getRule(): Rule
    {
        return new UseLoopStaticProxiesRule(new RulesConfig(
            blockingFunctions: true,
            loopStaticProxies: false,
            loopInstances: true,
            passLoopInterface: true,
            loopInterfaceProperties: true,
            asyncAwaitInClosure: true,
            asyncAwaitReachableViaCallStack: true,
        ));
    }

    #[Test]
    public function testNothingReported(): void
    {
        $method = [...ListLoopMethods::listAllLoopMethods()][0];
        $this->analyse([$method->file], []);
    }
}
