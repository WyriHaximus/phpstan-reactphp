<?php

declare(strict_types=1);

namespace WyriHaximus\Tests\React\PHPStan\Rules;

use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use WyriHaximus\React\PHPStan\Rules\WrapAwaitingClosuresInAsyncRule;
use WyriHaximus\Tests\React\PHPStan\Support\EnabledRulesConfig;

use function array_map;
use function dirname;

use const DIRECTORY_SEPARATOR;

/** @template-extends RuleTestCase<WrapAwaitingClosuresInAsyncRule> */
final class WrapAwaitingClosuresInAsyncRuleTest extends RuleTestCase
{
    /** @return list<string> */
    public static function getAdditionalConfigFiles(): array
    {
        return [dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'extension.neon'];
    }

    protected function getRule(): Rule
    {
        return new WrapAwaitingClosuresInAsyncRule(EnabledRulesConfig::get());
    }

    /** @return iterable<string, array{string, list<int>}> */
    public static function provideClosures(): iterable
    {
        yield 'closure' => ['await-in-closure.php', [11]];
        yield 'arrow function' => ['await-in-arrow-function.php', [10]];
        yield 'immediately invoked closure' => ['await-in-immediately-invoked-closure.php', [11]];
        yield 'closure assigned to a variable' => ['await-in-closure-assigned-to-a-variable.php', [12]];
        yield 'nested closure invoked synchronously' => ['await-in-nested-closure-invoked-synchronously.php', [14]];
        yield 'nested closure invoked later' => ['await-in-nested-closure-invoked-later.php', [12]];
        yield 'async called without a closure' => ['async-without-a-closure.php', [8]];
        yield 'closure wrapped in async' => ['await-in-async-closure.php', []];
        yield 'outside any closure' => ['await-outside-any-closure.php', []];
        yield 'function like declared in a closure' => ['await-inside-a-function-like-declared-in-a-closure.php', []];
        yield 'await from another namespace' => ['await-from-another-namespace.php', []];
    }

    /** @param list<int> $lines */
    #[DataProvider('provideClosures')]
    public function testClosures(string $fixture, array $lines): void
    {
        $this->analyse(
            [dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'utils' . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'async' . DIRECTORY_SEPARATOR . $fixture],
            array_map(
                static fn (int $line): array => [
                    WrapAwaitingClosuresInAsyncRule::MESSAGE,
                    $line,
                    WrapAwaitingClosuresInAsyncRule::TIP,
                ],
                $lines,
            ),
        );
    }
}
