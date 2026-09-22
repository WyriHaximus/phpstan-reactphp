<?php // phpcs:disable

declare(strict_types=1);

use React\EventLoop\Loop;

/**
 * @package react/event-loop
 * @replacement React\EventLoop\Loop::addTimer
 * @url https://reactphp.org/event-loop/#addtimer
 */
Loop::get()->addTimer(1.23, static fn (): null => null);

$loop = Loop::get();

/**
 * @package react/event-loop
 * @replacement React\EventLoop\Loop::addTimer
 * @url https://reactphp.org/event-loop/#addtimer
 */
$loop->addTimer(1.23, static fn (): null => null);
