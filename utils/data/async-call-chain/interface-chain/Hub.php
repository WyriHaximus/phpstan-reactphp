<?php // phpcs:disable

declare(strict_types=1);

namespace WyriHaximus\React\PHPStan\Data\AsyncCallChain\InterfaceChain;

final class Hub
{
    public Store|null $maybeStore = null;

    public function __construct(private Store $store)
    {
    }

    public function attachNoAwait(callable $tick): void
    {
        $tick(function (): string {
            return $this->store->label();
        });
    }

    public function attach(callable $tick): void
    {
        $tick(function (): int {
            return $this->store->snapshot();
        });
    }

    public function attachNullsafe(callable $tick): void
    {
        $tick(function (): int|null {
            return $this->maybeStore?->snapshot();
        });
    }

    public function attachMixedCase(callable $tick): void
    {
        $tick(function (): int {
            return $this->store->SnapShot();
        });
    }
}
