<?php

declare(strict_types=1);

namespace WyriHaximus\React\PHPStan\Config;

final readonly class RulesConfig
{
    public bool $asyncParserEnabled;
    public bool $asyncCallGraphEnabled;

    public function __construct(
        public bool $blockingFunctions,
        public bool $loopStaticProxies,
        public bool $loopInstances,
        public bool $passLoopInterface,
        public bool $loopInterfaceProperties,
        public bool $asyncAwaitInClosure,
        public bool $asyncAwaitReachableViaCallStack,
    ) {
        $this->asyncParserEnabled    = $this->asyncAwaitInClosure || $this->asyncAwaitReachableViaCallStack;
        $this->asyncCallGraphEnabled = $this->asyncAwaitReachableViaCallStack;
    }
}
