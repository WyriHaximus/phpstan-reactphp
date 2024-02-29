<?php

declare(strict_types=1);

namespace WyriHaximus\React\PHPStan\Utils;

final readonly class Func
{
    public function __construct(
        public string $name,
        public string $file,
        public string|null $package,
        public string|null $replacement,
        public string|null $url,
        public string $error,
        public int $line,
    ) {
    }
}
