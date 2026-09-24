<?php // phpcs:disable

declare(strict_types=1);

use function React\Async\await;
use function React\Promise\resolve;

$promise = resolve(true);

$promise->then(static fn (): bool => await($promise));
