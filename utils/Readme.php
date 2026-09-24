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
    private const string HEADER_FUNCTIONS     = '# Functions';
    private const string HEADER_EVENT_LOOP    = '# Event loop';
    private const string HEADER_ASYNC         = '# Async';
    private const string HEADER_CONFIGURATION = '# Configuration';
    private const string HEADER_LICENSE       = '# License';

    private const string ASYNC_PACKAGE     = '[react/async](https://github.com/reactphp/async)';
    private const string ASYNC_REPLACEMENT = 'React\Async\async';
    private const string ASYNC_URL         = 'https://reactphp.org/async/#async';

    private const string IDENTIFIER_AWAIT_WITHOUT_ASYNC         = 'wyrihaximus.reactphp.async.awaitWithoutAsync';
    private const string IDENTIFIER_AWAIT_REACHED_WITHOUT_ASYNC = 'wyrihaximus.reactphp.async.awaitReachedWithoutAsync';

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
            if (str_contains($eventLoopBody, self::HEADER_ASYNC)) {
                [$eventLoopBody] = explode(self::HEADER_ASYNC, $eventLoopBody, 2);
            }

            if (str_contains($eventLoopBody, self::HEADER_CONFIGURATION)) {
                [$eventLoopBody] = explode(self::HEADER_CONFIGURATION, $eventLoopBody, 2);
            }

            $eventLoopBlock = PHP_EOL . PHP_EOL . self::HEADER_EVENT_LOOP . rtrim($eventLoopBody) . PHP_EOL;
        }

        $configurationBlock = '';
        if (str_contains($middle, self::HEADER_CONFIGURATION)) {
            [, $configurationBody] = explode(self::HEADER_CONFIGURATION, $middle, 2);
            $configurationBlock    = PHP_EOL . PHP_EOL . self::HEADER_CONFIGURATION . rtrim($configurationBody) . PHP_EOL;
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
            implode(PHP_EOL, [...self::formatAsync()]),
            $configurationBlock,
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

        yield from self::formatDetails($func->package, $func->replacement, $func->url);
    }

    /** @return iterable<string> */
    private static function formatAsync(): iterable
    {
        yield self::HEADER_ASYNC;
        yield '';
        yield 'Every closure calling `React\Async\await` has to be wrapped in `React\Async\async` where that closure is';
        yield 'defined. Wrapping a closure further up the call stack, or wrapping it after the fact, does not count: by the';
        yield 'time the closure runs there is no guarantee it is still on the fiber the wrapping set up.';
        yield '';
        yield 'The same goes for closures reaching `React\Async\await` through the methods and functions they call: the';
        yield 'entire call stack starting in the closure runs on the fiber `React\Async\async` sets up, so every closure';
        yield 'leading to an await needs that wrapping.';
        yield '';
        yield 'See the [async documentation](https://reactphp.org/async/) and the [react/async](https://github.com/reactphp/async) package.';
        yield '';
        yield '## Await inside a non-async closure';
        yield '';
        yield 'PHPStan reports when `React\Async\await` is called inside a closure that is not wrapped in `React\Async\async` at the';
        yield 'point where that closure is defined.';
        yield '';
        yield 'Error identifier: `' . self::IDENTIFIER_AWAIT_WITHOUT_ASYNC . '`.';
        yield '';
        yield '## Await reached through the call stack';
        yield '';
        yield 'PHPStan reports when a non-async closure calls into code that eventually awaits, including through interfaces,';
        yield 'static methods, functions, and constructors. The error tip points at the await site further down the stack.';
        yield '';
        yield 'Error identifier: `' . self::IDENTIFIER_AWAIT_REACHED_WITHOUT_ASYNC . '`.';
        yield '';
        yield '## await';
        yield '';

        yield from self::formatDetails([self::ASYNC_PACKAGE], [self::ASYNC_REPLACEMENT], [self::ASYNC_URL]);
    }

    /**
     * @param array<string> $package
     * @param array<string> $replacement
     * @param array<string> $url
     *
     * @return iterable<string>
     */
    private static function formatDetails(array $package, array $replacement, array $url): iterable
    {
        if (count($package) > 0) {
            yield 'Relevant package(s):';
            yield '';

            foreach ($package as $singlePackage) {
                yield ' * ' . $singlePackage;
            }

            yield '';
        }

        if (count($replacement) > 0) {
            yield 'Suggested replacement(s):';
            yield '';

            foreach ($replacement as $singleReplacement) {
                yield ' * ' . $singleReplacement;
            }

            yield '';
        }

        if (count($url) <= 0) {
            return;
        }

        yield 'Documentation:';
        yield '';

        foreach ($url as $singleUrl) {
            yield ' * [' . $singleUrl . '](' . $singleUrl . ')';
        }

        yield '';
    }
}
