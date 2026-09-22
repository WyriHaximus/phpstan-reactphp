<?php // phpcs:disable

declare(strict_types=1);

use React\EventLoop\Loop;

/**
 * @package react/event-loop
 * @replacement React\EventLoop\Loop::addWriteStream
 * @url https://reactphp.org/event-loop/#addwritestream
 */
Loop::get()->addWriteStream(STDOUT, static fn (): null => null);

$loop = Loop::get();

/**
 * @package react/event-loop
 * @replacement React\EventLoop\Loop::addWriteStream
 * @url https://reactphp.org/event-loop/#addwritestream
 */
$loop->addWriteStream(STDOUT, static fn (): null => null);
