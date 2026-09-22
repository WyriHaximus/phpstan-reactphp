<?php // phpcs:disable

declare(strict_types=1);

use React\EventLoop\Loop;

/**
 * @package react/event-loop
 * @replacement React\EventLoop\Loop::run
 * @url https://reactphp.org/event-loop/#run
 */
Loop::get()->run();

$loop = Loop::get();

/**
 * @package react/event-loop
 * @replacement React\EventLoop\Loop::run
 * @url https://reactphp.org/event-loop/#run
 */
$loop->run();
