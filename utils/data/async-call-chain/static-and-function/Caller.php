<?php // phpcs:disable

declare(strict_types=1);

namespace WyriHaximus\React\PHPStan\Data\AsyncCallChain\StaticAndFunction;

final class Caller
{
    public function run(callable $tick): void
    {
        $tick(function (): int {
            $loaded = Loader::load();

            return $loaded + fetch();
        });
    }

    public function runConstructor(callable $tick): void
    {
        $tick(function (): Awaiter {
            return new Awaiter();
        });
    }
}
