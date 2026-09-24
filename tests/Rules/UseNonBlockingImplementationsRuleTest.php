<?php

declare(strict_types=1);

namespace WyriHaximus\Tests\React\Rules;

use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use WyriHaximus\React\PHPStan\Rules\UseNonBlockingImplementationsRule;
use WyriHaximus\React\PHPStan\Utils\Func;
use WyriHaximus\React\PHPStan\Utils\ListFunctions;
use WyriHaximus\Tests\React\PHPStan\Support\EnabledRulesConfig;

use function dirname;

use const DIRECTORY_SEPARATOR;

/** @template-extends RuleTestCase<UseNonBlockingImplementationsRule> */
final class UseNonBlockingImplementationsRuleTest extends RuleTestCase
{
    protected function getRule(): Rule
    {
        return new UseNonBlockingImplementationsRule(EnabledRulesConfig::get());
    }

    /** @return iterable<array<Func>> */
    public static function listAllTheFunctions(): iterable
    {
        foreach (ListFunctions::listAllBlockingFunctions() as $function) {
            yield $function->name => [$function];
        }
    }

    #[DataProvider('listAllTheFunctions')]
    #[Test]
    public function allTheFunctions(Func $func): void
    {
        $this->analyse([$func->file], [
            [
                $func->error,
                $func->line,
            ],
        ]);
    }

    #[Test]
    public function testCallsThatDoNotBlock(): void
    {
        $this->analyse([
            dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'utils' . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'functions' . DIRECTORY_SEPARATOR . 'edge-cases' . DIRECTORY_SEPARATOR . 'not-reported.php',
        ], []);
    }
}
