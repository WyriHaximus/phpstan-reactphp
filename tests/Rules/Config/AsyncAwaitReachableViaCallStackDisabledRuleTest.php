<?php

declare(strict_types=1);

namespace WyriHaximus\Tests\React\PHPStan\Rules\Config;

use PhpParser\Node;
use PHPStan\Collectors\Collector;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use PHPUnit\Framework\Attributes\Test;
use WyriHaximus\React\PHPStan\Collectors\AsyncCallGraphCollector;
use WyriHaximus\React\PHPStan\Config\RulesConfig;
use WyriHaximus\React\PHPStan\Rules\WrapClosuresReachingAwaitInAsyncRule;

use function dirname;
use function glob;

use const DIRECTORY_SEPARATOR;

/** @template-extends RuleTestCase<WrapClosuresReachingAwaitInAsyncRule> */
final class AsyncAwaitReachableViaCallStackDisabledRuleTest extends RuleTestCase
{
    /** @return list<string> */
    public static function getAdditionalConfigFiles(): array
    {
        return [dirname(__DIR__, 3) . DIRECTORY_SEPARATOR . 'extension.neon'];
    }

    protected function getRule(): Rule
    {
        return new WrapClosuresReachingAwaitInAsyncRule(new RulesConfig(
            blockingFunctions: true,
            loopStaticProxies: true,
            loopInstances: true,
            passLoopInterface: true,
            loopInterfaceProperties: true,
            asyncAwaitInClosure: true,
            asyncAwaitReachableViaCallStack: false,
        ));
    }

    /** @return list<Collector<Node, mixed>> */
    protected function getCollectors(): array
    {
        return [
            new AsyncCallGraphCollector(new RulesConfig(
                blockingFunctions: true,
                loopStaticProxies: true,
                loopInstances: true,
                passLoopInterface: true,
                loopInterfaceProperties: true,
                asyncAwaitInClosure: true,
                asyncAwaitReachableViaCallStack: false,
            )),
        ];
    }

    #[Test]
    public function testNothingReported(): void
    {
        $files = glob(dirname(__DIR__, 3) . DIRECTORY_SEPARATOR . 'utils' . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'async-call-chain' . DIRECTORY_SEPARATOR . 'interface-chain' . DIRECTORY_SEPARATOR . '*.php');
        self::assertIsArray($files);
        $this->analyse($files, []);
    }
}
