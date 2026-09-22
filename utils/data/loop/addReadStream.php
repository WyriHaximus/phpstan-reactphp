<?php // phpcs:disable

declare(strict_types=1);

use React\EventLoop\Loop;

/**
 * @package react/event-loop
 * @replacement React\EventLoop\Loop::addReadStream
 * @url https://reactphp.org/event-loop/#addreadstream
 */
Loop::get()->addReadStream(STDIN, static fn (): null => null);

$loop = Loop::get();

/**
 * @package react/event-loop
 * @replacement React\EventLoop\Loop::addReadStream
 * @url https://reactphp.org/event-loop/#addreadstream
 */
$loop->addReadStream(STDIN, static fn (): null => null);
