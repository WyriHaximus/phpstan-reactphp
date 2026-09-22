<?php // phpcs:disable

declare(strict_types=1);

use React\EventLoop\Loop;

/**
 * @package react/event-loop
 * @replacement React\EventLoop\Loop::futureTick
 * @url https://reactphp.org/event-loop/#futuretick
 */
Loop::get()->futureTick(static fn (): null => null);

$loop = Loop::get();

/**
 * @package react/event-loop
 * @replacement React\EventLoop\Loop::futureTick
 * @url https://reactphp.org/event-loop/#futuretick
 */
$loop->futureTick(static fn (): null => null);
