<?php // phpcs:disable

declare(strict_types=1);

/**
 * @package react/stream
 * @replacement React\Stream\WritableStreamInterface::write
 * @url https://reactphp.org/stream/#write
 */
fwrite($fd, 'stuff');
