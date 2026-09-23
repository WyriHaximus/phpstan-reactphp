<?php // phpcs:disable

declare(strict_types=1);

use React\EventLoop\LoopInterface;

final class HoldsLoopInterfaceProperty
{
    private object $notLoop, $notLoopToo;

    private LoopInterface $loop;

    private LoopInterface $paired, $pairedToo;

    private static LoopInterface $staticLoop;

    private ?LoopInterface $nullableLoop;

    private LoopInterface|string $unionLoop;

    /** @var LoopInterface */
    private $docBlockLoop;
}

final class HoldsPromotedLoopInterfaceProperty
{
    public function __construct(
        int $count,
        private readonly LoopInterface $loop,
        private readonly LoopInterface $secondLoop,
    ) {
    }

    public function notAConstructor(): void
    {
    }
}
