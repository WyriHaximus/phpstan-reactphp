<?php // phpcs:disable

declare(strict_types=1);

use React\EventLoop\LoopInterface;

final class HoldsNoLoopInterfaceProperty
{
    private object $objectProperty;

    public function __construct(LoopInterface $loop)
    {
    }

    public function run(LoopInterface $loop): void
    {
    }
}

final class EmptyClass
{
}
