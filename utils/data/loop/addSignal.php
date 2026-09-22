<?php // phpcs:disable

declare(strict_types=1);

use React\EventLoop\Loop;

/**
 * @package react/event-loop
 * @replacement React\EventLoop\Loop::addSignal
 * @url https://reactphp.org/event-loop/#addsignal
 */
Loop::get()->addSignal(SIGINT, static fn (): null => null);

$loop = Loop::get();

/**
 * @package react/event-loop
 * @replacement React\EventLoop\Loop::addSignal
 * @url https://reactphp.org/event-loop/#addsignal
 */
$loop->addSignal(SIGINT, static fn (): null => null);
