<?php // phpcs:disable

declare(strict_types=1);

use React\EventLoop\Loop;
use React\EventLoop\Timer\Timer;

$timer = new Timer(1.23, static fn (): null => null);

/**
 * @package react/event-loop
 * @replacement React\EventLoop\Loop::cancelTimer
 * @url https://reactphp.org/event-loop/#canceltimer
 */
Loop::get()->cancelTimer($timer);

$loop = Loop::get();

/**
 * @package react/event-loop
 * @replacement React\EventLoop\Loop::cancelTimer
 * @url https://reactphp.org/event-loop/#canceltimer
 */
$loop->cancelTimer($timer);
