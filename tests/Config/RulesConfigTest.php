<?php

declare(strict_types=1);

namespace WyriHaximus\Tests\React\PHPStan\Config;

use WyriHaximus\React\PHPStan\Config\RulesConfig;
use WyriHaximus\TestUtilities\TestCase;

final class RulesConfigTest extends TestCase
{
    public function testAllEnabled(): void
    {
        $config = new RulesConfig(
            blockingFunctions: true,
            loopStaticProxies: true,
            loopInstances: true,
            passLoopInterface: true,
            loopInterfaceProperties: true,
            asyncAwaitInClosure: true,
            asyncAwaitReachableViaCallStack: true,
        );

        self::assertTrue($config->blockingFunctions);
        self::assertTrue($config->loopStaticProxies);
        self::assertTrue($config->loopInstances);
        self::assertTrue($config->passLoopInterface);
        self::assertTrue($config->loopInterfaceProperties);
        self::assertTrue($config->asyncAwaitInClosure);
        self::assertTrue($config->asyncAwaitReachableViaCallStack);
        self::assertTrue($config->asyncParserEnabled);
        self::assertTrue($config->asyncCallGraphEnabled);
    }

    public function testAsyncParserEnabledWhenOnlyDirectAwait(): void
    {
        $config = new RulesConfig(
            blockingFunctions: true,
            loopStaticProxies: true,
            loopInstances: true,
            passLoopInterface: true,
            loopInterfaceProperties: true,
            asyncAwaitInClosure: true,
            asyncAwaitReachableViaCallStack: false,
        );

        self::assertTrue($config->asyncParserEnabled);
        self::assertFalse($config->asyncCallGraphEnabled);
    }

    public function testAsyncParserDisabledWhenBothAsyncRulesOff(): void
    {
        $config = new RulesConfig(
            blockingFunctions: true,
            loopStaticProxies: true,
            loopInstances: true,
            passLoopInterface: true,
            loopInterfaceProperties: true,
            asyncAwaitInClosure: false,
            asyncAwaitReachableViaCallStack: false,
        );

        self::assertFalse($config->asyncParserEnabled);
        self::assertFalse($config->asyncCallGraphEnabled);
    }
}
