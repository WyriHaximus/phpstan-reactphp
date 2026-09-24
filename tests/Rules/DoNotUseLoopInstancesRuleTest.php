<?php

declare(strict_types=1);

namespace WyriHaximus\Tests\React\PHPStan\Rules;

use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use WyriHaximus\React\PHPStan\Rules\DoNotUseLoopInstancesRule;
use WyriHaximus\React\PHPStan\Utils\ListLoopMethods;
use WyriHaximus\React\PHPStan\Utils\LoopMethod;
use WyriHaximus\Tests\React\PHPStan\Support\EnabledRulesConfig;

use function dirname;

use const DIRECTORY_SEPARATOR;

/** @template-extends RuleTestCase<DoNotUseLoopInstancesRule> */
final class DoNotUseLoopInstancesRuleTest extends RuleTestCase
{
    /** @return list<string> */
    public static function getAdditionalConfigFiles(): array
    {
        return [dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'extension.neon'];
    }

    protected function getRule(): Rule
    {
        return new DoNotUseLoopInstancesRule(EnabledRulesConfig::get());
    }

    /** @return iterable<array<LoopMethod>> */
    public static function listAllTheLoopMethods(): iterable
    {
        foreach (ListLoopMethods::listAllLoopMethods() as $loopMethod) {
            yield $loopMethod->name => [$loopMethod];
        }
    }

    #[DataProvider('listAllTheLoopMethods')]
    public function testAllTheLoopMethods(LoopMethod $loopMethod): void
    {
        $this->analyse([$loopMethod->file], [
            [
                $loopMethod->instanceError,
                $loopMethod->instanceLine,
                $loopMethod->tip,
            ],
        ]);
    }

    public function testCallsThatAreNotOnALoopInstance(): void
    {
        $this->analyse([
            dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'utils' . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'loop-edge-cases' . DIRECTORY_SEPARATOR . 'not-reported.php',
        ], []);
    }
}
