<?php

declare(strict_types=1);

namespace WyriHaximus\React\PHPStan\Utils;

final readonly class LoopMethod
{
    public function __construct(
        public string $name,
        public string $file,
        public string $staticProxyError,
        public int $staticProxyLine,
        public string $tip,
    ) {
    }
}
