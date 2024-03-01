<?php

declare(strict_types=1);

namespace WyriHaximus\React\PHPStan\Utils;

final readonly class Func
{
    /**
     * @param array<string> $package
     * @param array<string> $replacement
     * @param array<string> $url
     */
    public function __construct(
        public string $name,
        public string $file,
        public array $package,
        public array $replacement,
        public array $url,
        public string $error,
        public int $line,
    ) {
    }
}
