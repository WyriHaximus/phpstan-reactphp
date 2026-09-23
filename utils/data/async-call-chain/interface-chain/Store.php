<?php // phpcs:disable

declare(strict_types=1);

namespace WyriHaximus\React\PHPStan\Data\AsyncCallChain\InterfaceChain;

interface Store
{
    public function label(): string;

    public function snapshot(): int;
}
