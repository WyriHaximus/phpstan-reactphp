<?php // phpcs:disable

declare(strict_types=1);

use React\EventLoop\Loop;

/**
 * @package react/event-loop
 * @replacement React\EventLoop\Loop::removeWriteStream
 * @url https://reactphp.org/event-loop/#removewritestream
 */
Loop::get()->removeWriteStream(STDOUT);

$loop = Loop::get();

/**
 * @package react/event-loop
 * @replacement React\EventLoop\Loop::removeWriteStream
 * @url https://reactphp.org/event-loop/#removewritestream
 */
$loop->removeWriteStream(STDOUT);
