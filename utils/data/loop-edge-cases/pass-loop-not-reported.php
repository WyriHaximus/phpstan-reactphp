<?php // phpcs:disable

declare(strict_types=1);

final class NotALoop
{
    public static function get(): self
    {
        return new self();
    }
}

function acceptLoop(object $loop): void
{
}

acceptLoop(NotALoop::get());

$notLoop = new NotALoop();
acceptLoop($notLoop);

acceptLoop(new NotALoop());

$items = [NotALoop::get(), new NotALoop()];
acceptLoop(...$items);

$partial = acceptLoop(...);
