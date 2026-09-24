<?php // phpcs:disable

declare(strict_types=1);

namespace WyriHaximus\React\PHPStan\Data\AsyncCallChain\NothingReported;

final class Recursive
{
    public function ping(): int
    {
        return $this->pong();
    }

    public function pong(): int
    {
        return $this->ping();
    }
}
