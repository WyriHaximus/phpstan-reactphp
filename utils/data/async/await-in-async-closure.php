<?php // phpcs:disable

declare(strict_types=1);

use function React\Async\async;
use function React\Async\await;
use function React\Promise\resolve;

$promise = resolve(true);

$promise->then(async(static function () use ($promise): bool {
    return await($promise);
}));

$promise->then(async(static fn (): bool => await($promise)));

$wrap = async(...);
