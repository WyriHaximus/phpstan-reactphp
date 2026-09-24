<?php

declare(strict_types=1);

namespace WyriHaximus\Tests\React\PHPStan\Rules;

use PhpParser\Node;
use PHPStan\Collectors\Collector;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use WyriHaximus\React\PHPStan\Collectors\AsyncCallGraphCollector;
use WyriHaximus\React\PHPStan\Rules\WrapClosuresReachingAwaitInAsyncRule;
use WyriHaximus\Tests\React\PHPStan\Support\EnabledRulesConfig;

use function dirname;
use function glob;
use function sprintf;

use const DIRECTORY_SEPARATOR;

/** @template-extends RuleTestCase<WrapClosuresReachingAwaitInAsyncRule> */
final class WrapClosuresReachingAwaitInAsyncRuleTest extends RuleTestCase
{
    private const string NS = 'WyriHaximus\React\PHPStan\Data\AsyncCallChain\\';

    /** @return list<string> */
    public static function getAdditionalConfigFiles(): array
    {
        return [dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'extension.neon'];
    }

    protected function getRule(): Rule
    {
        return new WrapClosuresReachingAwaitInAsyncRule(EnabledRulesConfig::get());
    }

    /** @return list<Collector<Node, mixed>> */
    protected function getCollectors(): array
    {
        return [new AsyncCallGraphCollector(EnabledRulesConfig::get())];
    }

    /** @return iterable<string, array{string, list<array{0: string, 1: int, 2: string}>}> */
    public static function provideCallChains(): iterable
    {
        $snapshot = self::NS . 'InterfaceChain\Store::snapshot()';
        $count    = self::NS . 'InterfaceChain\RedisStore::count()';
        $redis    = self::dir('interface-chain') . 'RedisStore.php';

        yield 'through an interface, directly, nullsafe and in another case' => [
            'interface-chain',
            [
                self::err($snapshot, 25, $count, $redis, 30),
                self::err($snapshot, 32, $count, $redis, 30),
                self::err(self::NS . 'InterfaceChain\Store::SnapShot()', 39, $count, $redis, 30),
            ],
        ];

        $load      = self::NS . 'StaticAndFunction\Loader::load()';
        $construct = self::NS . 'StaticAndFunction\Awaiter::__construct()';
        $dir       = self::dir('static-and-function');

        yield 'through a static method, a function and a constructor' => [
            'static-and-function',
            [
                self::err($load, 12, $load, $dir . 'Loader.php', 15),
                self::err('fetch()', 14, 'fetch()', $dir . 'fetch.php', 11),
                self::err($construct, 21, $construct, $dir . 'Awaiter.php', 13),
            ],
        ];

        yield 'calls that never reach an await' => ['nothing-reported', []];
    }

    /** @param list<array{0: string, 1: int, 2: string}> $errors */
    #[DataProvider('provideCallChains')]
    public function testCallChains(string $scenario, array $errors): void
    {
        $files = glob(self::dir($scenario) . '*.php');
        self::assertIsArray($files);

        $this->analyse($files, $errors);
    }

    /** @return array{0: string, 1: int, 2: string} */
    private static function err(string $callee, int $line, string $awaiting, string $awaitFile, int $awaitLine): array
    {
        return [
            sprintf(WrapClosuresReachingAwaitInAsyncRule::MESSAGE, $callee),
            $line,
            sprintf(WrapClosuresReachingAwaitInAsyncRule::TIP, $awaiting, $awaitFile, $awaitLine),
        ];
    }

    private static function dir(string $scenario): string
    {
        return dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'utils' . DIRECTORY_SEPARATOR . 'data' .
            DIRECTORY_SEPARATOR . 'async-call-chain' . DIRECTORY_SEPARATOR . $scenario . DIRECTORY_SEPARATOR;
    }
}
