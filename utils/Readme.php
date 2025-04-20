<?php

declare(strict_types=1);

namespace WyriHaximus\React\PHPStan\Utils;

use function array_map;
use function count;
use function dirname;
use function explode;
use function file_get_contents;
use function file_put_contents;
use function implode;

use const DIRECTORY_SEPARATOR;
use const PHP_EOL;

final class Readme
{
    private const HEADER_FUNCTIONS = '# Functions';
    private const HEADER_LICENSE   = '# License';

    public static function update(Func ...$funcs): void
    {
        $readmePath            = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'README.md';
        $readme                = file_get_contents($readmePath);
        [$beforeFunctionList]  = explode(self::HEADER_FUNCTIONS, $readme);
        [, $afterFunctionList] = explode(self::HEADER_LICENSE, $readme);

        file_put_contents($readmePath, implode('', [
            $beforeFunctionList,
            self::HEADER_FUNCTIONS,
            PHP_EOL,
            implode(PHP_EOL, array_map(static fn (Func $func): string => implode(PHP_EOL, [...self::formatFunc($func)]), $funcs)),
            PHP_EOL,
            PHP_EOL,
            self::HEADER_LICENSE,
            $afterFunctionList,
        ]));
    }

    /** @return iterable<string> */
    private static function formatFunc(Func $func): iterable
    {
        yield '';
        yield '## ' . $func->name;
        yield '';

        if (count($func->package) > 0) {
            yield 'Relevant package(s):';
            yield '';

            foreach ($func->package as $package) {
                yield ' * ' . $package;
            }

            yield '';
        }

        if (count($func->replacement) > 0) {
            yield 'Suggested replacement(s):';
            yield '';

            foreach ($func->replacement as $replacement) {
                yield ' * ' . $replacement;
            }

            yield '';
        }

        if (count($func->url) <= 0) {
            return;
        }

        yield 'Documentation:';
        yield '';

        foreach ($func->url as $url) {
            yield ' * [' . $url . '](' . $url . ')';
        }

        yield '';
    }
}
