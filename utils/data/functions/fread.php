<?php // phpcs:disable

declare(strict_types=1);

/**
 * @package react/stream
 * @replacement React\Stream\ReadableStreamInterface::on
 * @url https://reactphp.org/stream/#data-event
 */
fread($fd, 1024);
