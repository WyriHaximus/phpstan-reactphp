<?php

declare(strict_types=1);

namespace WyriHaximus\React\PHPStan\Utils;

use RuntimeException;

use function array_map;
use function count;
use function dirname;
use function explode;
use function file_get_contents;
use function file_put_contents;
use function implode;
use function is_string;
use function rtrim;
use function str_contains;

use const DIRECTORY_SEPARATOR;
use const PHP_EOL;

final class Readme
{
    private const string HEADER_FUNCTIONS  = '# Functions';
    private const string HEADER_EVENT_LOOP = '# Event loop';
    private const string HEADER_LICENSE    = '# License';

    public static function update(Func ...$funcs): void
    {
        $readmePath = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'README.md';
        $readme     = file_get_contents($readmePath); /** @phpstan-ignore wyrihaximus.reactphp.blocking.function.fileGetContents */
        if (! is_string($readme)) {
            throw new RuntimeException('Unable to read README');
        }

        [$beforeFunctionList, $afterFunctionListMarker] = explode(self::HEADER_FUNCTIONS, $readme, 2);
        [$middle, $afterLicense]                        = explode(self::HEADER_LICENSE, $afterFunctionListMarker, 2);

        $eventLoopBlock = '';
        if (str_contains($middle, self::HEADER_EVENT_LOOP)) {
            [, $eventLoopBody] = explode(self::HEADER_EVENT_LOOP, $middle, 2);
            $eventLoopBlock    = PHP_EOL . PHP_EOL . self::HEADER_EVENT_LOOP . rtrim($eventLoopBody) . PHP_EOL;
        }

        /** @phpstan-ignore wyrihaximus.reactphp.blocking.function.filePutContents */
        file_put_contents($readmePath, implode('', [
            $beforeFunctionList,
            self::HEADER_FUNCTIONS,
            PHP_EOL,
            implode(PHP_EOL, array_map(static fn (Func $func): string => implode(PHP_EOL, [...self::formatFunc($func)]), $funcs)),
            $eventLoopBlock,
            PHP_EOL,
            PHP_EOL,
            self::HEADER_LICENSE,
            $afterLicense,
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
