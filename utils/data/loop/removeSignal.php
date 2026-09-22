<?php // phpcs:disable

declare(strict_types=1);

use React\EventLoop\Loop;

$listener = static fn (): null => null;

/**
 * @package react/event-loop
 * @replacement React\EventLoop\Loop::removeSignal
 * @url https://reactphp.org/event-loop/#removesignal
 */
Loop::get()->removeSignal(SIGINT, $listener);

$loop = Loop::get();

/**
 * @package react/event-loop
 * @replacement React\EventLoop\Loop::removeSignal
 * @url https://reactphp.org/event-loop/#removesignal
 */
$loop->removeSignal(SIGINT, $listener);
