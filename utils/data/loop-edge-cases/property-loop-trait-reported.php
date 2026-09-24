<?php // phpcs:disable

declare(strict_types=1);

use React\EventLoop\LoopInterface;

trait HoldsLoopInterfacePropertyInTrait
{
    public function notAProperty(): void
    {
    }

    private LoopInterface $traitLoop, $secondTraitLoop;
}
