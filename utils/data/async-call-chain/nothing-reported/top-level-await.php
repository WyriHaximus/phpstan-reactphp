<?php // phpcs:disable

declare(strict_types=1);

use WyriHaximus\React\PHPStan\Data\AsyncCallChain\NothingReported\Awaiter;

use function React\Async\await;

await((new Awaiter())->promise);
