<?php // phpcs:disable

declare(strict_types=1);

use React\EventLoop\Loop;

/**
 * @package react/event-loop
 * @replacement React\EventLoop\Loop::stop
 * @url https://reactphp.org/event-loop/#stop
 */
Loop::get()->stop();

$loop = Loop::get();

/**
 * @package react/event-loop
 * @replacement React\EventLoop\Loop::stop
 * @url https://reactphp.org/event-loop/#stop
 */
$loop->stop();
