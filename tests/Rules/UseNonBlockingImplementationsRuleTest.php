<?php

declare(strict_types=1);

namespace WyriHaximus\Tests\React\Rules;

use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use WyriHaximus\React\PHPStan\Rules\UseNonBlockingImplementationsRule;
use WyriHaximus\React\PHPStan\Utils\Func;
use WyriHaximus\React\PHPStan\Utils\ListFunctions;

/** @template-extends RuleTestCase<UseNonBlockingImplementationsRule> */
final class UseNonBlockingImplementationsRuleTest extends RuleTestCase
{
    protected function getRule(): Rule
    {
        return new UseNonBlockingImplementationsRule();
    }

    /** @return iterable<array<Func>> */
    public static function listAllTheFunctions(): iterable
    {
        foreach (ListFunctions::listAllBlockingFunctions() as $function) {
            yield $function->name => [$function];
        }
    }

    #[DataProvider('listAllTheFunctions')]
    public function testAllTheFunctions(Func $func): void
    {
        $this->analyse([$func->file], [
            [
                $func->error,
                $func->line,
            ],
        ]);
    }
}
