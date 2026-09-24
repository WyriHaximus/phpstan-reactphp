<?php

declare(strict_types=1);

namespace WyriHaximus\Tests\React\PHPStan\Rules\Config;

use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use PHPUnit\Framework\Attributes\Test;
use WyriHaximus\React\PHPStan\Config\RulesConfig;
use WyriHaximus\React\PHPStan\Rules\UseNonBlockingImplementationsRule;
use WyriHaximus\React\PHPStan\Utils\ListFunctions;

use function dirname;

use const DIRECTORY_SEPARATOR;

/** @template-extends RuleTestCase<UseNonBlockingImplementationsRule> */
final class BlockingFunctionsDisabledRuleTest extends RuleTestCase
{
    /** @return list<string> */
    public static function getAdditionalConfigFiles(): array
    {
        return [dirname(__DIR__, 3) . DIRECTORY_SEPARATOR . 'extension.neon'];
    }

    protected function getRule(): Rule
    {
        return new UseNonBlockingImplementationsRule(new RulesConfig(
            blockingFunctions: false,
            loopStaticProxies: true,
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
        $function = [...ListFunctions::listAllBlockingFunctions()][0];
        $this->analyse([$function->file], []);
    }
}
