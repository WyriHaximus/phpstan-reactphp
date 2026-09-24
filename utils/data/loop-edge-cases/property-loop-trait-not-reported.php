<?php // phpcs:disable

declare(strict_types=1);

trait HoldsNoLoopInterfacePropertyInTrait
{
    public function noPropertyHere(): void
    {
    }

    private object $first, $second;
}
