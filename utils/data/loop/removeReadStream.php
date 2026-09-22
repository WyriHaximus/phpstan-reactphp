<?php // phpcs:disable

declare(strict_types=1);

use React\EventLoop\Loop;

/**
 * @package react/event-loop
 * @replacement React\EventLoop\Loop::removeReadStream
 * @url https://reactphp.org/event-loop/#removereadstream
 */
Loop::get()->removeReadStream(STDIN);

$loop = Loop::get();

/**
 * @package react/event-loop
 * @replacement React\EventLoop\Loop::removeReadStream
 * @url https://reactphp.org/event-loop/#removereadstream
 */
$loop->removeReadStream(STDIN);
