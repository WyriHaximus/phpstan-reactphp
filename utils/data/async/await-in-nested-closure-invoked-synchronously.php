<?php // phpcs:disable

declare(strict_types=1);

use React\Promise\PromiseInterface;

use function React\Async\async;
use function React\Async\await;
use function React\Promise\resolve;

$promise = resolve(true);

async(static function () use ($promise): array {
    return array_map(static fn (PromiseInterface $each): bool => await($each), [$promise]);
})();
