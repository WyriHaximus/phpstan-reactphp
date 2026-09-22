<?php

declare(strict_types=1);

namespace WyriHaximus\Tests\React\PHPStan\Rules;

use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use WyriHaximus\React\PHPStan\Rules\DoNotPassLoopInterfaceRule;

use function dirname;

use const DIRECTORY_SEPARATOR;

/** @template-extends RuleTestCase<DoNotPassLoopInterfaceRule> */
final class DoNotPassLoopInterfaceRuleTest extends RuleTestCase
{
    private const string MESSAGE = 'Passing a React\EventLoop\LoopInterface instance is prohibited, use the static proxies on React\EventLoop\Loop from react/event-loop instead.';
    private const string TIP     = 'Please consult the documentation for more information: https://reactphp.org/event-loop/';

    /** @return list<string> */
    public static function getAdditionalConfigFiles(): array
    {
        return [dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'extension.neon'];
    }

    protected function getRule(): Rule
    {
        return new DoNotPassLoopInterfaceRule();
    }

    public function testPassingLoopInterfaceIsReported(): void
    {
        $this->analyse([
            dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'utils' . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'loop-edge-cases' . DIRECTORY_SEPARATOR . 'pass-loop-reported.php',
        ], [
            [self::MESSAGE, 24, self::TIP],
            [self::MESSAGE, 28, self::TIP],
            [self::MESSAGE, 37, self::TIP],
            [self::MESSAGE, 46, self::TIP],
            [self::MESSAGE, 55, self::TIP],
            [self::MESSAGE, 57, self::TIP],
            [self::MESSAGE, 59, self::TIP],
            [self::MESSAGE, 59, self::TIP],
        ]);
    }

    public function testCallsThatDoNotPassLoopInterface(): void
    {
        $this->analyse([
            dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'utils' . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'loop-edge-cases' . DIRECTORY_SEPARATOR . 'pass-loop-not-reported.php',
        ], []);
    }
}
