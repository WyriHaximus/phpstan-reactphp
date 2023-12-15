<?php

declare(strict_types=1);

namespace WyriHaximus\React\PHPStan\Utils;

final readonly class Func
{
    public function __construct(
        public string $name,
        public string $file,
        public string $package,
        public string $replacement,
        public string $url,
        public string $error,
        public int $line,
    ) {
    }
}
