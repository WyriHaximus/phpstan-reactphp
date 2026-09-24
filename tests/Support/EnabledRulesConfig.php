<?php

declare(strict_types=1);

namespace WyriHaximus\Tests\React\PHPStan\Support;

use WyriHaximus\React\PHPStan\Config\RulesConfig;

final class EnabledRulesConfig
{
    public static function get(): RulesConfig
    {
        return new RulesConfig(
            blockingFunctions: true,
            loopStaticProxies: true,
            loopInstances: true,
            passLoopInterface: true,
            loopInterfaceProperties: true,
            asyncAwaitInClosure: true,
            asyncAwaitReachableViaCallStack: true,
        );
    }
}
