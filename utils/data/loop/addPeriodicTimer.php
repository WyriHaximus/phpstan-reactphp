<?php // phpcs:disable

declare(strict_types=1);

use React\EventLoop\Loop;

/**
 * @package react/event-loop
 * @replacement React\EventLoop\Loop::addPeriodicTimer
 * @url https://reactphp.org/event-loop/#addperiodictimer
 */
Loop::get()->addPeriodicTimer(1.23, static fn (): null => null);

$loop = Loop::get();

/**
 * @package react/event-loop
 * @replacement React\EventLoop\Loop::addPeriodicTimer
 * @url https://reactphp.org/event-loop/#addperiodictimer
 */
$loop->addPeriodicTimer(1.23, static fn (): null => null);
